#!/usr/bin/env bash
set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$APP_DIR"

WEB_USER="${WEB_USER:-$(whoami)}"
WEB_GROUP="${WEB_GROUP:-$(id -gn)}"

echo "==> Fixing permissions for ${WEB_USER}:${WEB_GROUP}..."

mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/testing \
    storage/logs \
    storage/app/public \
    storage/app/private \
    bootstrap/cache

touch storage/logs/laravel.log

if [[ "$(id -u)" -eq 0 ]]; then
    chown -R "${WEB_USER}:${WEB_GROUP}" storage bootstrap/cache
fi

chmod -R ug+rwX storage bootstrap/cache
chmod 664 storage/logs/laravel.log 2>/dev/null || true

echo "==> Done. storage/ and bootstrap/cache/ are writable."
