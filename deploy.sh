#!/usr/bin/env bash
set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$APP_DIR"

echo "==> Deploying $(basename "$APP_DIR")..."

if [[ "${SKIP_GIT_PULL:-0}" != "1" ]] && git rev-parse --is-inside-work-tree >/dev/null 2>&1; then
    echo "==> Pulling latest changes..."
    git pull --ff-only origin main
fi

echo "==> Installing PHP dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

echo "==> Installing Node dependencies..."
npm ci

echo "==> Building frontend assets..."
npm run build

echo "==> Running database migrations..."
php artisan migrate --force

echo "==> Ensuring Redis is running..."
if command -v redis-cli >/dev/null 2>&1; then
    if ! redis-cli ping >/dev/null 2>&1; then
        if command -v systemctl >/dev/null 2>&1; then
            sudo systemctl start redis-server || sudo systemctl start redis || true
        else
            sudo service redis-server start || sudo service redis start || true
        fi
    fi

    if redis-cli ping >/dev/null 2>&1; then
        echo "Redis is ready."
    else
        echo "WARNING: Redis is not responding. Cache and sessions may fail until Redis is started."
    fi
fi

echo "==> Optimizing application..."
php artisan optimize

echo "==> Ensuring upload directory exists..."
mkdir -p storage/app/public

echo "==> Fixing storage permissions..."
bash "$APP_DIR/fix-permissions.sh"

echo "==> Restarting queue workers..."
php artisan queue:restart || true

if command -v systemctl >/dev/null 2>&1; then
    for service in php8.3-fpm php8.2-fpm php-fpm; do
        if systemctl is-enabled "$service" >/dev/null 2>&1; then
            echo "==> Reloading $service..."
            sudo systemctl reload "$service" || true
            break
        fi
    done
fi

echo "==> Deployment complete."
