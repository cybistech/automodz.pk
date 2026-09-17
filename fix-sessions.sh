#!/usr/bin/env bash
# Quick production fix for "Failed to open stream: Permission denied" on sessions/uploads.
set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$APP_DIR"

USER_NAME="${WEB_USER:-$(whoami)}"
GROUP_NAME="${WEB_GROUP:-$(id -gn "$USER_NAME" 2>/dev/null || id -gn)}"

echo "Fixing Laravel writable paths in: $APP_DIR"
echo "Owner target: ${USER_NAME}:${GROUP_NAME}"

mkdir -p \
  storage/framework/sessions \
  storage/framework/cache/data \
  storage/framework/views \
  storage/framework/temp \
  storage/logs \
  uploads/products/thumbs \
  uploads/categories \
  storage/app/public/products/thumbs \
  storage/app/public/categories \
  bootstrap/cache

chown -R "${USER_NAME}:${GROUP_NAME}" storage bootstrap/cache 2>/dev/null || true
chmod -R ug+rwX storage bootstrap/cache
chmod -R 777 \
  storage/framework/sessions \
  storage/framework/cache \
  storage/framework/views \
  storage/framework/temp \
  storage/logs \
  uploads \
  bootstrap/cache

php artisan uploads:link --force 2>/dev/null || true

php artisan optimize:clear 2>/dev/null || true

echo "Done. Test by reloading the site."
