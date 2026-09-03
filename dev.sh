#!/usr/bin/env bash
set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$APP_DIR"

echo "==> AutoModz local dev setup"

mkdir -p storage/app/public/products storage/app/public/categories

if [[ -z "$(grep -E '^APP_KEY=base64:' .env 2>/dev/null || true)" ]]; then
    echo "==> Generating APP_KEY..."
    php artisan key:generate --force
fi

if ! php artisan db:show >/dev/null 2>&1; then
    cat <<'EOF'

Database connection failed.

Update .env with your local MySQL credentials, then create the database:

  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=automodz
  DB_USERNAME=root
  DB_PASSWORD=your_mysql_password

Then run (replace password if needed):

  mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS automodz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
  php artisan migrate --seed

EOF
    exit 1
fi

php artisan migrate --force
php artisan config:clear
php artisan cache:clear

echo ""
echo "==> Starting dev server at http://127.0.0.1:8000"
echo "    Press Ctrl+C to stop."
echo ""

php artisan serve --host=127.0.0.1 --port=8000
