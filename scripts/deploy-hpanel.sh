#!/usr/bin/env bash
set -euo pipefail

# Simple deploy script for Hostinger hPanel Git deployments.
# Run this on the shared hosting server.

REPO_DIR="/home/u586998430/domains/natalcode.com.br/public_html"
BRANCH="main"
TAG_ON_DEPLOY="${TAG_ON_DEPLOY:-0}"
DEPLOY_LOG="${DEPLOY_LOG:-/home/u586998430/logs/deployments.log}"

cd "$REPO_DIR"

# Ensure we are on the correct branch
git fetch --all --prune
git checkout "$BRANCH"

# Save current revision for quick rollback
PREV_REV="$(git rev-parse HEAD)"
echo "Previous revision: $PREV_REV"

# Pull latest changes
git pull --ff-only origin "$BRANCH"

# Optional tag on deploy (requires push access)
if [ "$TAG_ON_DEPLOY" = "1" ]; then
  TAG="v$(date -u +%Y%m%d-%H%M)"
  git tag -a "$TAG" -m "Deploy $TAG"
  if git push origin "$TAG"; then
    echo "Pushed tag: $TAG"
  else
    echo "Warning: could not push tag $TAG (missing credentials?)"
  fi
fi

# Log deploy
if [ -n "$DEPLOY_LOG" ]; then
  mkdir -p "$(dirname "$DEPLOY_LOG")"
  printf "%s %s %s\n" "$(date -u +%Y-%m-%dT%H:%M:%SZ)" "$(git rev-parse HEAD)" "${TAG:-"-"}" >> "$DEPLOY_LOG"
fi

# Install PHP dependencies if composer.json exists
if [ -f composer.json ]; then
  if command -v composer >/dev/null 2>&1; then
    composer install --no-dev --optimize-autoloader
  else
    echo "composer not found; skipping composer install"
  fi
fi

# Clear DI/cache if present
if [ -d var/cache ]; then
  rm -rf var/cache
  mkdir -p var/cache
  chmod 775 var/cache
fi

echo "Deploy complete. Current revision: $(git rev-parse HEAD)"
echo "Rollback: git reset --hard $PREV_REV"
