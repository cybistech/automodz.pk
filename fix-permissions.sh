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

if [[ "$(id -u)" -eq 0 ]]; then
    chown -R "${WEB_USER}:${WEB_GROUP}" storage bootstrap/cache
fi

chmod -R ug+rwX storage bootstrap/cache
chmod 664 storage/logs/laravel.log 2>/dev/null || true

for dir in storage/framework/views storage/framework/cache bootstrap/cache storage/framework/temp; do
    if [[ -w "$dir" ]]; then
        echo "OK  $dir is writable"
    else
        echo "ERR $dir is NOT writable — fix ownership for your PHP/web user"
        exit 1
    fi
done

echo "==> Done. storage/ and bootstrap/cache/ are writable."
