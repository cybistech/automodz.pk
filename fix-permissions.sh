#!/usr/bin/env bash
set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$APP_DIR"

detect_web_user() {
    if [[ -n "${WEB_USER:-}" ]]; then
        echo "$WEB_USER"
        return
    fi

    # cPanel / home hosting: /home/<user>/public_html/...
    if [[ "$APP_DIR" =~ ^/home/([^/]+)/ ]]; then
        local cpanel_user="${BASH_REMATCH[1]}"
        if id "$cpanel_user" >/dev/null 2>&1; then
            echo "$cpanel_user"
            return
        fi
    fi

    # Prefer the PHP-FPM / Apache / LiteSpeed worker user.
    local detected=""
    detected="$(ps -o user= -C php-fpm8.3,php-fpm8.2,php-fpm,lsphp,apache2,httpd 2>/dev/null | awk 'NF && $1!="root"{print $1; exit}')" || true

    if [[ -z "$detected" ]]; then
        detected="$(ps aux 2>/dev/null | awk '/php-fpm: pool|lsphp|apache2|httpd/{print $1}' | awk '$1!="root" && $1!="USER"{print; exit}')" || true
    fi

    if [[ -n "$detected" ]] && id "$detected" >/dev/null 2>&1; then
        echo "$detected"
        return
    fi

    if id www-data >/dev/null 2>&1; then
        echo "www-data"
        return
    fi

    if id nobody >/dev/null 2>&1; then
        echo "nobody"
        return
    fi

    whoami
}

detect_web_group() {
    if [[ -n "${WEB_GROUP:-}" ]]; then
        echo "$WEB_GROUP"
        return
    fi

    local user="$1"
    id -gn "$user" 2>/dev/null || id -gn
}

WEB_USER="$(detect_web_user)"
WEB_GROUP="$(detect_web_group "$WEB_USER")"

echo "==> App: ${APP_DIR}"
echo "==> Fixing permissions for ${WEB_USER}:${WEB_GROUP}..."

mkdir -p \
    storage/app/public/products \
    storage/app/public/products/thumbs \
    storage/app/public/categories \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/testing \
    storage/framework/temp \
    storage/logs \
    storage/app/public \
    storage/app/private \
    bootstrap/cache

# Public URL path /uploads must point at storage/app/public (symlink).
if [[ -L uploads ]]; then
    echo "OK  uploads symlink exists -> $(readlink uploads)"
elif [[ -e uploads ]]; then
    echo "WARN uploads exists but is not a symlink; leave it alone"
else
    ln -s storage/app/public uploads
    echo "OK  created uploads -> storage/app/public"
fi

touch storage/logs/laravel.log
touch "storage/logs/laravel-$(date +%F).log" 2>/dev/null || true

# Own storage as the account/PHP user whenever possible.
chown -R "${WEB_USER}:${WEB_GROUP}" storage bootstrap/cache 2>/dev/null || true

find storage bootstrap/cache -type d -exec chmod 775 {} + 2>/dev/null || chmod -R 775 storage bootstrap/cache
find storage bootstrap/cache -type f -exec chmod 664 {} + 2>/dev/null || true

# These must always be writable by PHP (sessions especially).
WRITABLE_DIRS=(
    storage/app/public
    storage/app/public/products
    storage/app/public/products/thumbs
    storage/app/public/categories
    storage/framework
    storage/framework/temp
    storage/framework/cache
    storage/framework/cache/data
    storage/framework/sessions
    storage/framework/views
    storage/logs
    bootstrap/cache
)

for dir in "${WRITABLE_DIRS[@]}"; do
    mkdir -p "$dir"
    chown "${WEB_USER}:${WEB_GROUP}" "$dir" 2>/dev/null || true
    # 777 fallback for hosts where PHP user != deploy user.
    chmod 777 "$dir" 2>/dev/null || chmod 775 "$dir" 2>/dev/null || true
done

check_writable() {
    local dir="$1"
    local probe="$dir/.perm-write-test-$$"

    if ( : > "$probe" ) 2>/dev/null; then
        rm -f "$probe" 2>/dev/null || true
        return 0
    fi

    chmod 777 "$dir" 2>/dev/null || true

    if ( : > "$probe" ) 2>/dev/null; then
        rm -f "$probe" 2>/dev/null || true
        return 0
    fi

    return 1
}

FAILED=0
for dir in \
    storage/logs \
    storage/framework/views \
    storage/framework/cache \
    storage/framework/sessions \
    bootstrap/cache \
    storage/framework/temp \
    storage/app/public/products \
    storage/app/public/products/thumbs \
    storage/app/public/categories
do
    if check_writable "$dir"; then
        echo "OK  $dir is writable"
    else
        echo "ERR $dir is NOT writable"
        FAILED=1
    fi
done

if [[ "$FAILED" -ne 0 ]]; then
    echo
    echo "Permission fix incomplete. On the server run:"
    echo "  cd ${APP_DIR}"
    echo "  chmod -R 777 storage bootstrap/cache"
    echo "  chown -R ${WEB_USER}:${WEB_GROUP} storage bootstrap/cache"
    exit 1
fi

echo "==> Done. storage is writable for ${WEB_USER}:${WEB_GROUP}."
echo "    If sessions still fail, run on the live server:"
echo "    chmod -R 777 storage/framework/sessions storage/framework/cache storage/logs bootstrap/cache"
