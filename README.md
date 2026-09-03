# AutoModz — Auto & Motorcycle Mods

**Domain:** [automodz.pk](https://automodz.pk)

Pakistan's premium e-commerce platform for auto and motorcycle parts, performance mods, lights, mirrors, and accessories.

## Features

- **Auto & moto catalog** — DRL lights, fog lights, indicators, mirrors, holders, and more
- **Guest checkout** — Order without an account; track by order number + email/phone
- **Multi-method login** — Email, mobile OTP, Google & Facebook SSO
- **Sale pricing** — Discounted PKR prices with homepage deals section
- **Full SEO** — Meta titles, descriptions, keywords per product
- **Payments** — JazzCash, Stripe, bank transfer, COD

## Deployment

Point your domain document root to the project folder (not `public/`). Apache uses the root `.htaccess` and `index.php`.

```bash
./deploy.sh
```

Set `SKIP_GIT_PULL=1` to deploy the current checkout without pulling from git.

## Quick Start

Requires MySQL 8+ and Redis:

```bash
sudo systemctl start redis-server
mysql -u root -e "CREATE DATABASE automodz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
mkdir -p storage/app/public
npm run build && php artisan serve
```

## Demo Accounts

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@automodz.pk | password |
| Customer | customer@automodz.pk | password |

## Production (.env)

```env
APP_NAME="AutoModz"
APP_URL=https://automodz.pk
SITE_DOMAIN=automodz.pk
SITE_EMAIL=info@automodz.pk

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=automodz
DB_USERNAME=root
DB_PASSWORD=your_mysql_password

CACHE_STORE=failover
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1
```

`failover` uses Redis first, then falls back to file cache if Redis is down.

Install and start Redis on the server:

```bash
sudo apt install redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server
redis-cli ping   # should return PONG
```

Create the database once on the server:

```bash
mysql -u root -p -e "CREATE DATABASE automodz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate --seed
```

## License

MIT
