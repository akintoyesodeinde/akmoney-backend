#!/usr/bin/env bash
set -Eeuo pipefail

# Wrapper script invoked by SSM Document AkMoney-Deploy-Backend
IMAGE_TAG="${1:-}"
RUN_MIGRATIONS="${2:-true}"

exec /opt/akmoney/deploy-release.sh backend "$IMAGE_TAG" "$RUN_MIGRATIONS"
