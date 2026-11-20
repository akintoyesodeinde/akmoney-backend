#!/usr/bin/env bash
set -Eeuo pipefail

# ==============================================================================
# AkMoney Production EC2 User Data Bootstrap Script (Phase 11)
# Target OS: Amazon Linux 2023 (x86_64)
# Instance Type: t3.micro (1 GiB RAM)
# Authentication: EC2 IAM Instance Profile (akmoney-ec2-instance-profile) + IMDSv2
# Zero embedded secrets — all configuration fetched via AWS SSM Parameter Store
# ==============================================================================

LOG_FILE="/var/log/akmoney-bootstrap.log"
exec > >(tee -a "$LOG_FILE") 2>&1

echo "================================================================="
echo "[$(date -u +'%Y-%m-%dT%H:%M:%SZ')] Starting AkMoney EC2 Bootstrap"
echo "================================================================="

# 1. Update operating system packages & install Docker + required utilities (curl-minimal is pre-installed on AL2023)
dnf update -y
dnf install -y docker amazon-ssm-agent awscli jq git tar gzip util-linux logrotate mariadb105

# 2. Configure 1 GiB Swap File to protect t3.micro (1 GiB RAM) against OOM spikes
if [ ! -f /swapfile ]; then
    echo "Configuring 1 GiB swapfile for t3.micro memory stability..."
    dd if=/dev/zero of=/swapfile bs=1M count=1024
    chmod 600 /swapfile
    mkswap /swapfile
    swapon /swapfile
    echo '/swapfile swap swap defaults 0 0' >> /etc/fstab
fi

# 3. Install verified Docker Compose v2 CLI plugin
mkdir -p /usr/local/lib/docker/cli-plugins
if [ ! -x /usr/local/lib/docker/cli-plugins/docker-compose ]; then
    COMPOSE_VERSION="v2.29.2"
    curl -fsSL "https://github.com/docker/compose/releases/download/${COMPOSE_VERSION}/docker-compose-linux-$(uname -m)" \
        -o /usr/local/lib/docker/cli-plugins/docker-compose
    chmod +x /usr/local/lib/docker/cli-plugins/docker-compose
fi

# 4. Verify AWS CLI availability
aws --version

# 5. Enable and start Amazon SSM Agent & Docker
systemctl enable --now amazon-ssm-agent
systemctl enable --now docker

# 6. Create application deployment directories & configure permissions
APP_DIR="/opt/akmoney"
mkdir -p "${APP_DIR}/releases" \
         "${APP_DIR}/logs" \
         "${APP_DIR}/storage/app/public" \
         "${APP_DIR}/storage/framework/cache/data" \
         "${APP_DIR}/storage/framework/sessions" \
         "${APP_DIR}/storage/framework/views" \
         "${APP_DIR}/storage/logs" \
         "${APP_DIR}/certs" \
         /var/lock

chmod 750 "${APP_DIR}"
chown -R 82:82 "${APP_DIR}/storage" || true

# Download AWS RDS Global TLS CA Bundle for encrypted MySQL connections
curl -fsSL https://truststore.pki.rds.amazonaws.com/global/global-bundle.pem \
    -o "${APP_DIR}/certs/rds-global-bundle.pem"
chmod 644 "${APP_DIR}/certs/rds-global-bundle.pem"

# 7. Install host-locked deployment, rollback, and health-check scripts
cat << 'HEALTH_EOF' > "${APP_DIR}/health-check.sh"
#!/usr/bin/env bash
set -Eeuo pipefail
TARGET="${1:-all}"
check_backend() {
    for i in {1..30}; do
        RESPONSE=$(docker exec akmoney-backend-prod curl -fsS http://127.0.0.1:8000/api/health 2>/dev/null || true)
        if echo "$RESPONSE" | grep -q '"status":"ok"'; then
            echo "Backend health check PASSED: $RESPONSE"
            return 0
        fi
        sleep 2
    done
    echo "ERROR: Backend health check FAILED." >&2
    return 1
}
check_frontend() {
    for i in {1..20}; do
        if curl -fsS http://127.0.0.1/health | grep -q "healthy"; then
            echo "Frontend Nginx health endpoint (/health) PASSED."
            return 0
        fi
        sleep 2
    done
    echo "ERROR: Frontend health check FAILED." >&2
    return 1
}
case "$TARGET" in
    backend) check_backend ;;
    frontend) check_frontend ;;
    all)
        check_backend
        check_frontend
        curl -fsS http://127.0.0.1/api/health | grep -q '"status":"ok"'
        ;;
esac
HEALTH_EOF

cat << 'ROLLBACK_EOF' > "${APP_DIR}/rollback-release.sh"
#!/usr/bin/env bash
set -Eeuo pipefail
SERVICE="${1:-}"
APP_DIR="/opt/akmoney"
PREV_FILE="${APP_DIR}/releases/previous-${SERVICE}.json"
CURR_FILE="${APP_DIR}/releases/current-${SERVICE}.json"
if [[ ! -f "$PREV_FILE" ]]; then
    echo "ERROR: No previous release metadata at $PREV_FILE." >&2
    exit 1
fi
PREV_IMAGE=$(jq -r '.deployed_image // empty' "$PREV_FILE")
cd "$APP_DIR"
if [[ "$SERVICE" == "backend" ]]; then
    export BACKEND_IMAGE="$PREV_IMAGE"
    docker compose -f docker-compose.production.yml up -d --no-deps backend scheduler
    "${APP_DIR}/health-check.sh" backend
    cp "$PREV_FILE" "$CURR_FILE"
elif [[ "$SERVICE" == "frontend" ]]; then
    export FRONTEND_IMAGE="$PREV_IMAGE"
    docker compose -f docker-compose.production.yml up -d --no-deps frontend
    "${APP_DIR}/health-check.sh" frontend
    cp "$PREV_FILE" "$CURR_FILE"
fi
ROLLBACK_EOF

cat << 'DEPLOY_EOF' > "${APP_DIR}/deploy-release.sh"
#!/usr/bin/env bash
set -Eeuo pipefail
SERVICE="${1:-}"
IMAGE_TAG="${2:-}"
RUN_MIGRATIONS="${3:-false}"
APP_DIR="/opt/akmoney"
LOCK_FILE="/var/lock/akmoney-deploy.lock"
if [[ ! "$IMAGE_TAG" =~ ^[a-zA-Z0-9._-]+/akmoney-${SERVICE}(:[a-f0-9]{7,40}|@sha256:[a-f0-9]{64})$ ]]; then
    echo "ERROR: Invalid immutable image reference '$IMAGE_TAG'." >&2
    exit 1
fi
exec 200>"$LOCK_FILE"
flock -w 300 200
RELEASE_FILE="${APP_DIR}/releases/current-${SERVICE}.json"
PREV_FILE="${APP_DIR}/releases/previous-${SERVICE}.json"
IMDS_TOKEN=$(curl -fsSL -X PUT "http://169.254.169.254/latest/api/token" -H "X-aws-ec2-metadata-token-ttl-seconds: 900")
AWS_REGION=$(curl -fsSL -H "X-aws-ec2-metadata-token: ${IMDS_TOKEN}" "http://169.254.169.254/latest/meta-data/placement/region")
export AWS_DEFAULT_REGION="${AWS_REGION}"
get_ssm_param() {
    aws ssm get-parameter --name "$1" --with-decryption --query "Parameter.Value" --output text 2>/dev/null || echo "${2:-}"
}
DOCKERHUB_USER=$(get_ssm_param "/akmoney/prod/registry/dockerhub-username" "akintoyesodeinde")
DOCKERHUB_PULL_TOKEN=$(get_ssm_param "/akmoney/prod/registry/dockerhub-pull-token" "")
if [[ -n "$DOCKERHUB_PULL_TOKEN" ]]; then
    printf "%s" "$DOCKERHUB_PULL_TOKEN" | docker login --username "$DOCKERHUB_USER" --password-stdin
    unset DOCKERHUB_PULL_TOKEN
fi
if ! docker image inspect "$IMAGE_TAG" >/dev/null 2>&1; then
    docker pull "$IMAGE_TAG"
fi
docker logout || true
rm -f /root/.docker/config.json
[[ -f "$RELEASE_FILE" ]] && cp "$RELEASE_FILE" "$PREV_FILE"
cd "$APP_DIR"
if [[ "$SERVICE" == "backend" ]]; then
    export BACKEND_IMAGE="$IMAGE_TAG"
    docker compose -f docker-compose.production.yml up -d --no-deps backend scheduler
    if [[ "$RUN_MIGRATIONS" == "true" ]]; then
        docker exec akmoney-backend-prod php artisan migrate --force || { "${APP_DIR}/rollback-release.sh" backend; exit 1; }
    fi
    "${APP_DIR}/health-check.sh" backend || { "${APP_DIR}/rollback-release.sh" backend; exit 1; }
elif [[ "$SERVICE" == "frontend" ]]; then
    export FRONTEND_IMAGE="$IMAGE_TAG"
    docker compose -f docker-compose.production.yml up -d --no-deps frontend
    "${APP_DIR}/health-check.sh" frontend || { "${APP_DIR}/rollback-release.sh" frontend; exit 1; }
fi
IMAGE_DIGEST=$(docker inspect --format='{{index .RepoDigests 0}}' "$IMAGE_TAG" 2>/dev/null || echo "$IMAGE_TAG")
cat << EOF > "$RELEASE_FILE"
{
  "service": "$SERVICE",
  "deployed_image": "$IMAGE_TAG",
  "image_digest": "$IMAGE_DIGEST",
  "deployed_at": "$(date -u +'%Y-%m-%dT%H:%M:%SZ')"
}
EOF
DEPLOY_EOF

cat << 'BACKEND_WRAP_EOF' > "${APP_DIR}/deploy-backend.sh"
#!/usr/bin/env bash
set -Eeuo pipefail
exec /opt/akmoney/deploy-release.sh backend "${1:-}" "${2:-true}"
BACKEND_WRAP_EOF

cat << 'FRONTEND_WRAP_EOF' > "${APP_DIR}/deploy-frontend.sh"
#!/usr/bin/env bash
set -Eeuo pipefail
exec /opt/akmoney/deploy-release.sh frontend "${1:-}" "false"
FRONTEND_WRAP_EOF

chmod +x "${APP_DIR}/health-check.sh" \
         "${APP_DIR}/rollback-release.sh" \
         "${APP_DIR}/deploy-release.sh" \
         "${APP_DIR}/deploy-backend.sh" \
         "${APP_DIR}/deploy-frontend.sh"

# 8. Resolve Region via IMDSv2 token & fetch runtime SSM parameters
IMDS_TOKEN=$(curl -fsSL -X PUT "http://169.254.169.254/latest/api/token" -H "X-aws-ec2-metadata-token-ttl-seconds: 21600")
AWS_REGION=$(curl -fsSL -H "X-aws-ec2-metadata-token: ${IMDS_TOKEN}" "http://169.254.169.254/latest/meta-data/placement/region")
export AWS_DEFAULT_REGION="${AWS_REGION}"

get_ssm_param() {
    aws ssm get-parameter --name "$1" --with-decryption --query "Parameter.Value" --output text 2>/dev/null || echo "${2:-}"
}

APP_KEY=$(get_ssm_param "/akmoney/prod/backend/app-key" "")
APP_URL=$(get_ssm_param "/akmoney/prod/backend/app-url" "http://localhost")
APP_ENV=$(get_ssm_param "/akmoney/prod/backend/app-env" "production")
DB_HOST=$(get_ssm_param "/akmoney/prod/backend/db-host" "127.0.0.1")
DB_PORT=$(get_ssm_param "/akmoney/prod/backend/db-port" "3306")
DB_NAME=$(get_ssm_param "/akmoney/prod/backend/db-name" "akmoney")
DB_USER=$(get_ssm_param "/akmoney/prod/backend/db-username" "akmoney_app")
DB_PASS=$(get_ssm_param "/akmoney/prod/backend/db-password" "")
PAYMENT_API_KEY=$(get_ssm_param "/akmoney/prod/backend/payment-api-key" "")
WEBHOOK_SECRET=$(get_ssm_param "/akmoney/prod/backend/webhook-secret" "")

cat << EOF > "${APP_DIR}/.env.backend"
APP_NAME=AkMoney
APP_ENV=${APP_ENV}
APP_KEY=${APP_KEY}
APP_DEBUG=false
APP_URL=${APP_URL}
LOG_CHANNEL=stack
LOG_LEVEL=info
DB_CONNECTION=mysql
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT}
DB_DATABASE=${DB_NAME}
DB_USERNAME=${DB_USER}
DB_PASSWORD=${DB_PASS}
MYSQL_ATTR_SSL_CA=/etc/ssl/certs/rds-global-bundle.pem
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
QUEUE_CONNECTION=database
CACHE_STORE=database
PAYMENT_API_KEY=${PAYMENT_API_KEY}
WEBHOOK_SECRET=${WEBHOOK_SECRET}
CORS_ALLOWED_ORIGINS=${APP_URL}
EOF
chmod 600 "${APP_DIR}/.env.backend"

# 9. Install production Docker Compose configuration
DOCKERHUB_USER=$(get_ssm_param "/akmoney/prod/registry/dockerhub-username" "akintoyesodeinde")

cat << 'COMPOSE_EOF' > "${APP_DIR}/docker-compose.production.yml"
services:
  frontend:
    image: ${FRONTEND_IMAGE:-akintoyesodeinde/akmoney-frontend:latest}
    container_name: akmoney-frontend-prod
    restart: unless-stopped
    ports:
      - "80:80"
    depends_on:
      backend:
        condition: service_healthy
    mem_limit: 128m
    logging:
      driver: "json-file"
      options:
        max-size: "10m"
        max-file: "3"
    healthcheck:
      test: ["CMD", "wget", "-q", "--spider", "http://127.0.0.1/health"]
      interval: 30s
      timeout: 5s
      retries: 3
    networks:
      - akmoney-private-net

  backend:
    image: ${BACKEND_IMAGE:-akintoyesodeinde/akmoney-backend:latest}
    container_name: akmoney-backend-prod
    restart: unless-stopped
    expose:
      - "8000"
    env_file:
      - /opt/akmoney/.env.backend
    volumes:
      - /opt/akmoney/storage:/var/www/html/storage
      - /opt/akmoney/certs/rds-global-bundle.pem:/etc/ssl/certs/rds-global-bundle.pem:ro
    mem_limit: 384m
    logging:
      driver: "json-file"
      options:
        max-size: "15m"
        max-file: "3"
    healthcheck:
      test: ["CMD", "curl", "-fsS", "http://127.0.0.1:8000/api/health"]
      interval: 30s
      timeout: 5s
      retries: 3
      start_period: 20s
    networks:
      - akmoney-private-net

  scheduler:
    image: ${BACKEND_IMAGE:-akintoyesodeinde/akmoney-backend:latest}
    container_name: akmoney-scheduler-prod
    restart: unless-stopped
    entrypoint: ["/bin/sh", "-c", "while true; do php /var/www/html/artisan schedule:run --verbose --no-interaction; sleep 60; done"]
    env_file:
      - /opt/akmoney/.env.backend
    volumes:
      - /opt/akmoney/storage:/var/www/html/storage
      - /opt/akmoney/certs/rds-global-bundle.pem:/etc/ssl/certs/rds-global-bundle.pem:ro
    mem_limit: 128m
    logging:
      driver: "json-file"
      options:
        max-size: "5m"
        max-file: "2"
    networks:
      - akmoney-private-net

networks:
  akmoney-private-net:
    driver: bridge
COMPOSE_EOF
chmod 640 "${APP_DIR}/docker-compose.production.yml"

# 10. Configure host log rotation
cat << 'LOGROTATE_EOF' > /etc/logrotate.d/akmoney
/opt/akmoney/logs/*.log /var/log/akmoney-bootstrap.log {
    daily
    rotate 7
    compress
    delaycompress
    missingok
    notifempty
    create 0640 root root
}
LOGROTATE_EOF

# 11. Authenticate to Docker Hub using read-only pull token & start containers
DOCKERHUB_PULL_TOKEN=$(get_ssm_param "/akmoney/prod/registry/dockerhub-pull-token" "")
if [ -n "$DOCKERHUB_PULL_TOKEN" ]; then
    printf "%s" "$DOCKERHUB_PULL_TOKEN" | docker login --username "$DOCKERHUB_USER" --password-stdin
    unset DOCKERHUB_PULL_TOKEN

    cd "${APP_DIR}"
    export BACKEND_IMAGE="${DOCKERHUB_USER}/akmoney-backend:latest"
    export FRONTEND_IMAGE="${DOCKERHUB_USER}/akmoney-frontend:latest"

    if docker compose -f docker-compose.production.yml pull; then
        docker compose -f docker-compose.production.yml up -d
        sleep 10
        if [ -n "$APP_KEY" ] && [ "$DB_HOST" != "127.0.0.1" ]; then
            docker exec akmoney-backend-prod php artisan migrate --force || true
        fi
    fi

    docker logout || true
    rm -f /root/.docker/config.json
fi

echo "================================================================="
echo "[$(date -u +'%Y-%m-%dT%H:%M:%SZ')] AkMoney EC2 Bootstrap Complete"
echo "================================================================="
