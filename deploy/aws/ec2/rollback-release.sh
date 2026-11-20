#!/usr/bin/env bash
set -Eeuo pipefail

# ==============================================================================
# AkMoney Controlled Application Rollback Script (Phase 15 & Phase 16)
# Usage: ./rollback-release.sh <service: backend|frontend>
# ==============================================================================

SERVICE="${1:-}"
APP_DIR="/opt/akmoney"
PREV_FILE="${APP_DIR}/releases/previous-${SERVICE}.json"
CURR_FILE="${APP_DIR}/releases/current-${SERVICE}.json"

if [[ "$SERVICE" != "backend" && "$SERVICE" != "frontend" ]]; then
    echo "ERROR: Service must be 'backend' or 'frontend'." >&2
    exit 1
fi

echo "================================================================="
echo "[$(date -u +'%Y-%m-%dT%H:%M:%SZ')] Initiating rollback for: $SERVICE"
echo "================================================================="

if [[ ! -f "$PREV_FILE" ]]; then
    echo "ERROR: No previous release metadata found at $PREV_FILE. Automated rollback stopped." >&2
    exit 1
fi

PREV_IMAGE=$(jq -r '.deployed_image // empty' "$PREV_FILE")
if [[ -z "$PREV_IMAGE" ]]; then
    echo "ERROR: Invalid previous image tag in $PREV_FILE." >&2
    exit 1
fi

echo "Restoring previous verified image: $PREV_IMAGE"
cd "$APP_DIR"

if [[ "$SERVICE" == "backend" ]]; then
    export BACKEND_IMAGE="$PREV_IMAGE"
    docker compose -f docker-compose.production.yml up -d --no-deps backend scheduler
    if "${APP_DIR}/health-check.sh" backend; then
        cp "$PREV_FILE" "$CURR_FILE"
        echo "Backend rollback to $PREV_IMAGE verified healthy."
    else
        echo "CRITICAL: Backend health check failed after rollback. Manual database/schema inspection required." >&2
        exit 1
    fi
elif [[ "$SERVICE" == "frontend" ]]; then
    export FRONTEND_IMAGE="$PREV_IMAGE"
    docker compose -f docker-compose.production.yml up -d --no-deps frontend
    if "${APP_DIR}/health-check.sh" frontend; then
        cp "$PREV_FILE" "$CURR_FILE"
        echo "Frontend rollback to $PREV_IMAGE verified healthy."
    else
        echo "CRITICAL: Frontend health check failed after rollback." >&2
        exit 1
    fi
fi
