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
    storage/framework/temp \
    storage/logs \
    storage/app/public \
    storage/app/private \
    bootstrap/cache

touch storage/logs/laravel.log
touch "storage/logs/laravel-$(date +%F).log" 2>/dev/null || true

if [[ "$(id -u)" -eq 0 ]]; then
    chown -R "${WEB_USER}:${WEB_GROUP}" storage bootstrap/cache
else
    chown -R "${WEB_USER}:${WEB_GROUP}" storage bootstrap/cache 2>/dev/null || true
fi

find storage bootstrap/cache -type d -exec chmod 775 {} + 2>/dev/null || chmod -R 775 storage bootstrap/cache
find storage bootstrap/cache -type f -exec chmod 664 {} + 2>/dev/null || true
chmod 775 storage/logs 2>/dev/null || true

for dir in storage/logs storage/framework/views storage/framework/cache bootstrap/cache storage/framework/temp; do
    if [[ -w "$dir" ]]; then
        echo "OK  $dir is writable"
    else
        echo "ERR $dir is NOT writable"
        echo "Run: sudo chown -R ${WEB_USER}:${WEB_GROUP} storage bootstrap/cache"
        echo "Run: chmod -R 775 storage bootstrap/cache"
        exit 1
    fi
done

echo "==> Done. storage/ and bootstrap/cache/ are writable."
