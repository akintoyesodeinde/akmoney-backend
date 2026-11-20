#!/usr/bin/env bash
set -Eeuo pipefail

# ==============================================================================
# AkMoney Host-Locked Release Deployment Script (Phase 15 & Phase 16)
# Usage: ./deploy-release.sh <service: backend|frontend> <image-tag> [run-migrations: true|false]
# Enforces:
# - Host-level flock serialization (/var/lock/akmoney-deploy.lock)
# - Approved repository & immutable SHA/digest tag validation
# - SSM runtime parameter synchronization
# - Health check validation & automated rollback on failure
# ==============================================================================

SERVICE="${1:-}"
IMAGE_TAG="${2:-}"
RUN_MIGRATIONS="${3:-false}"
APP_DIR="/opt/akmoney"
LOCK_FILE="/var/lock/akmoney-deploy.lock"

if [[ "$SERVICE" != "backend" && "$SERVICE" != "frontend" ]]; then
    echo "ERROR: Invalid service '$SERVICE'. Must be 'backend' or 'frontend'." >&2
    exit 1
fi

if [[ -z "$IMAGE_TAG" ]]; then
    echo "ERROR: Immutable image tag or digest is required." >&2
    exit 1
fi

# Validate allowed image repository pattern (<user>/akmoney-(backend|frontend):<sha|digest>)
if [[ ! "$IMAGE_TAG" =~ ^[a-zA-Z0-9._-]+/akmoney-${SERVICE}(:[a-f0-9]{7,40}|@sha256:[a-f0-9]{64})$ ]]; then
    echo "ERROR: Image reference '$IMAGE_TAG' does not match permitted immutable pattern for akmoney-${SERVICE}." >&2
    exit 1
fi

mkdir -p "${APP_DIR}/releases" "${APP_DIR}/logs" /var/lock

# Acquire exclusive host-level deployment lock (wait up to 300 seconds)
exec 200>"$LOCK_FILE"
if ! flock -w 300 200; then
    echo "ERROR: Could not acquire deployment lock ($LOCK_FILE) after 300 seconds." >&2
    exit 1
fi

echo "================================================================="
echo "[$(date -u +'%Y-%m-%dT%H:%M:%SZ')] Locked deployment started: $SERVICE"
echo "Target Image: $IMAGE_TAG"
echo "================================================================="

RELEASE_FILE="${APP_DIR}/releases/current-${SERVICE}.json"
PREV_FILE="${APP_DIR}/releases/previous-${SERVICE}.json"

# 1. Refresh runtime environment from AWS SSM Parameter Store if deploying backend
IMDS_TOKEN=$(curl -fsSL -X PUT "http://169.254.169.254/latest/api/token" -H "X-aws-ec2-metadata-token-ttl-seconds: 900")
AWS_REGION=$(curl -fsSL -H "X-aws-ec2-metadata-token: ${IMDS_TOKEN}" "http://169.254.169.254/latest/meta-data/placement/region")
export AWS_DEFAULT_REGION="${AWS_REGION}"

get_ssm_param() {
    aws ssm get-parameter --name "$1" --with-decryption --query "Parameter.Value" --output text 2>/dev/null || echo "${2:-}"
}

if [[ "$SERVICE" == "backend" ]]; then
    echo "Refreshing runtime configuration from AWS SSM Parameter Store..."
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
fi

# 2. Authenticate to Docker Hub using read-only pull token
DOCKERHUB_USER=$(get_ssm_param "/akmoney/prod/registry/dockerhub-username" "akintoyesodeinde")
DOCKERHUB_PULL_TOKEN=$(get_ssm_param "/akmoney/prod/registry/dockerhub-pull-token" "")
if [[ -n "$DOCKERHUB_PULL_TOKEN" ]]; then
    printf "%s" "$DOCKERHUB_PULL_TOKEN" | docker login --username "$DOCKERHUB_USER" --password-stdin
    unset DOCKERHUB_PULL_TOKEN
fi

# 3. Pull immutable target Docker image
echo "Pulling image: $IMAGE_TAG..."
docker pull "$IMAGE_TAG"
docker logout || true
rm -f /root/.docker/config.json

# 4. Preserve current release pointer for rollback
if [[ -f "$RELEASE_FILE" ]]; then
    cp "$RELEASE_FILE" "$PREV_FILE"
fi

# Load currently deployed sibling image so docker compose preserves both services
CURRENT_BACKEND=$(jq -r '.deployed_image // empty' "${APP_DIR}/releases/current-backend.json" 2>/dev/null || echo "${DOCKERHUB_USER}/akmoney-backend:latest")
CURRENT_FRONTEND=$(jq -r '.deployed_image // empty' "${APP_DIR}/releases/current-frontend.json" 2>/dev/null || echo "${DOCKERHUB_USER}/akmoney-frontend:latest")

cd "$APP_DIR"

if [[ "$SERVICE" == "backend" ]]; then
    export BACKEND_IMAGE="$IMAGE_TAG"
    export FRONTEND_IMAGE="${CURRENT_FRONTEND}"

    docker compose -f docker-compose.production.yml up -d --no-deps backend scheduler

    if [[ "$RUN_MIGRATIONS" == "true" ]]; then
        echo "Inspecting pending Laravel migrations..."
        docker exec akmoney-backend-prod php artisan migrate:status || true
        echo "Executing controlled production migrations (php artisan migrate --force)..."
        if ! docker exec akmoney-backend-prod php artisan migrate --force; then
            echo "ERROR: Database migration failed! Rolling back backend container..." >&2
            "${APP_DIR}/rollback-release.sh" backend || true
            exit 1
        fi
    fi

    if ! "${APP_DIR}/health-check.sh" backend; then
        echo "ERROR: Backend health check failed! Rolling back to previous release..." >&2
        "${APP_DIR}/rollback-release.sh" backend || true
        exit 1
    fi

elif [[ "$SERVICE" == "frontend" ]]; then
    export BACKEND_IMAGE="${CURRENT_BACKEND}"
    export FRONTEND_IMAGE="$IMAGE_TAG"

    docker compose -f docker-compose.production.yml up -d --no-deps frontend

    if ! "${APP_DIR}/health-check.sh" frontend; then
        echo "ERROR: Frontend health check failed! Rolling back to previous release..." >&2
        "${APP_DIR}/rollback-release.sh" frontend || true
        exit 1
    fi
fi

# 5. Record verified release metadata
IMAGE_DIGEST=$(docker inspect --format='{{index .RepoDigests 0}}' "$IMAGE_TAG" 2>/dev/null || echo "$IMAGE_TAG")
cat << EOF > "$RELEASE_FILE"
{
  "service": "$SERVICE",
  "deployed_image": "$IMAGE_TAG",
  "image_digest": "$IMAGE_DIGEST",
  "deployed_at": "$(date -u +'%Y-%m-%dT%H:%M:%SZ')"
}
EOF

echo "================================================================="
echo "[$(date -u +'%Y-%m-%dT%H:%M:%SZ')] Deployment succeeded: $SERVICE ($IMAGE_TAG)"
echo "================================================================="
