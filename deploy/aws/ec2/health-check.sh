#!/usr/bin/env bash
set -Eeuo pipefail

# ==============================================================================
# AkMoney Internal & External Health Check Script (Phase 15)
# Usage: ./health-check.sh [backend|frontend|all]
# ==============================================================================

TARGET="${1:-all}"

check_backend() {
    echo "Checking backend container health..."
    for i in {1..30}; do
        RESPONSE=$(docker exec akmoney-backend-prod curl -fsS http://127.0.0.1:8000/api/health 2>/dev/null || true)
        if echo "$RESPONSE" | grep -q '"status":"ok"'; then
            echo "Backend health check PASSED: $RESPONSE"
            return 0
        fi
        sleep 2
    done
    echo "ERROR: Backend health check FAILED after 60 seconds." >&2
    return 1
}

check_frontend() {
    echo "Checking frontend & Nginx reverse-proxy health..."
    for i in {1..20}; do
        if curl -fsS http://127.0.0.1/health | grep -q "healthy"; then
            echo "Frontend Nginx health endpoint (/health) PASSED."
            return 0
        fi
        sleep 2
    done
    echo "ERROR: Frontend health check FAILED after 40 seconds." >&2
    return 1
}

case "$TARGET" in
    backend)
        check_backend
        ;;
    frontend)
        check_frontend
        ;;
    all)
        check_backend
        check_frontend
        # Also verify frontend-to-backend /api/health proxy route
        if curl -fsS http://127.0.0.1/api/health | grep -q '"status":"ok"'; then
            echo "Reverse proxy (/api/health -> backend:8000) PASSED."
        else
            echo "ERROR: Reverse proxy /api/health check FAILED." >&2
            exit 1
        fi
        ;;
    *)
        echo "Usage: $0 [backend|frontend|all]" >&2
        exit 1
        ;;
esac
