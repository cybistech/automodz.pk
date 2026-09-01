# MotoModz — Motorcycle Parts & Mods

**Domain:** [motomodz.pk](https://motomodz.pk)

Pakistan's motorcycle-focused e-commerce platform for parts, mods, lights, mirrors, and accessories.

## Features

- **Motorcycle-focused catalog** — DRL lights, fog lights, indicators, mirrors, holders, and more
- **Guest checkout** — Order without an account; track by order number + email/phone
- **Multi-method login** — Email, mobile OTP, Google & Facebook SSO
- **Sale pricing** — Discounted PKR prices with homepage deals section
- **Full SEO** — Meta titles, descriptions, keywords per product
- **Payments** — JazzCash, Stripe, bank transfer, COD

## Quick Start

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed && php artisan storage:link
npm run build && php artisan serve
```

## Demo Accounts

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@motomodz.pk | password |
| Customer | customer@motomodz.pk | password |

## Production (.env)

```env
APP_NAME="MotoModz"
APP_URL=https://motomodz.pk
SITE_DOMAIN=motomodz.pk
SITE_EMAIL=support@motomodz.pk
```

## License

MIT
