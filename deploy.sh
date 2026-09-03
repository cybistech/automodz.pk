#!/usr/bin/env bash
set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$APP_DIR"

echo "==> Deploying $(basename "$APP_DIR")..."

# Laravel standard: these files are server-managed and must never be overwritten by deploy.
SERVER_MANAGED=(.env .htaccess vendor)

ensure_server_file() {
    local file="$1"
    local example="$2"

    if [[ ! -f "$file" && -f "$example" ]]; then
        cp "$example" "$file"
        echo "Created $file from $example"
    fi
}

ensure_server_file .env .env.example
ensure_server_file .htaccess .htaccess.example

if [[ ! -f .env ]]; then
    echo "ERROR: .env is missing. Copy .env.example to .env, configure production values, then redeploy."
    exit 1
fi

if [[ ! -f .htaccess ]]; then
    echo "ERROR: .htaccess is missing. Copy .htaccess.example to .htaccess, then redeploy."
    exit 1
fi

if [[ "${SKIP_GIT_PULL:-0}" != "1" ]] && git rev-parse --is-inside-work-tree >/dev/null 2>&1; then
    echo "==> Pulling latest changes (preserving .env, .htaccess, vendor)..."

    declare -A BACKUPS=()
    for file in "${SERVER_MANAGED[@]}"; do
        if [[ -e "$file" ]]; then
            BACKUPS[$file]=$(mktemp)
            cp -a "$file" "${BACKUPS[$file]}"
        fi
    done

    git pull --ff-only origin main

    for file in "${!BACKUPS[@]}"; do
        cp -a "${BACKUPS[$file]}" "$file"
        rm -f "${BACKUPS[$file]}"
    done
fi

echo "==> Installing PHP dependencies (vendor is built on server, never deployed)..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

echo "==> Fixing storage permissions..."
bash "$APP_DIR/fix-permissions.sh"

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
mkdir -p storage/app/public/products storage/app/public/categories

if [[ -z "$(find storage/app/public/products -maxdepth 1 -type f 2>/dev/null | head -1)" ]]; then
    echo "==> No product images found — generating catalog images..."
    php artisan db:seed --class=MotorcycleCatalogSeeder --force || echo "WARNING: Could not seed product images."
fi

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
