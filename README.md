# AutoPartsPro - Auto Parts E-Commerce Platform

A complete Laravel auto parts shopping platform with modern UI, admin panel, and multiple payment gateways.

## Features

- **Modern storefront** — Dark-themed responsive design with product search, filters, and shopping cart
- **Admin panel** — Upload auto parts with images, videos (upload or YouTube URL), specifications, and inventory
- **Payment methods**
  - JazzCash (mobile wallet)
  - Stripe (credit/debit cards)
  - Direct bank transfer
  - Cash on delivery (COD)
- **Clean URLs** — No `/public` in the URL (root `index.php` + `.htaccess`)
- **Auto deploy** — GitHub Actions CI/CD pipeline with SSH deployment

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+
- SQLite (default) or MySQL

## Quick Start

```bash
# Install dependencies
composer install
npm install

# Configure environment
cp .env.example .env
php artisan key:generate

# Database setup
touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link

# Build frontend
npm run build

# Start server (document root is project root, not public/)
php artisan serve
```

Visit `http://localhost:8000`

## Default Accounts

| Role     | Email                  | Password |
|----------|------------------------|----------|
| Admin    | admin@autoparts.com    | password |
| Customer | customer@example.com   | password |

## Admin Panel

Login as admin and visit `/admin` to:
- Manage products (with image/video upload)
- Manage categories
- View and update orders

## Payment Setup

### Stripe
1. Create account at [stripe.com](https://stripe.com)
2. Add keys to `.env`: `STRIPE_KEY`, `STRIPE_SECRET`

### JazzCash
1. Register at [JazzCash Merchant Portal](https://sandbox.jazzcash.com.pk)
2. Add credentials to `.env`: `JAZZCASH_MERCHANT_ID`, `JAZZCASH_PASSWORD`, `JAZZCASH_INTEGRITY_SALT`

### Bank Transfer
Configure your bank details in `.env` under `BANK_*` variables.

## Deployment

### Remove public from URL
The project is configured to run from the root directory:
- `index.php` in project root
- `.htaccess` routes all requests to Laravel

For Nginx, point `root` to the project directory and use:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### GitHub Actions Auto Deploy
1. Add secrets to your GitHub repository:
   - `DEPLOY_HOST` — Server IP/hostname
   - `DEPLOY_USER` — SSH username
   - `DEPLOY_SSH_KEY` — Private SSH key
   - `DEPLOY_PATH` — Path on server (e.g. `/var/www/autoparts`)
   - `DEPLOY_PORT` — SSH port (optional, default 22)
2. Push to `main` branch to trigger deploy

## License

MIT
