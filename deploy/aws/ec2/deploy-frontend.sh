#!/usr/bin/env bash
set -Eeuo pipefail

# Wrapper script invoked by SSM Document AkMoney-Deploy-Frontend
IMAGE_TAG="${1:-}"

exec /opt/akmoney/deploy-release.sh frontend "$IMAGE_TAG" "false"
