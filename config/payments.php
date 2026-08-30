<?php

return [
    'currency' => env('PAYMENT_CURRENCY', 'PKR'),

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'jazzcash' => [
        'merchant_id' => env('JAZZCASH_MERCHANT_ID'),
        'password' => env('JAZZCASH_PASSWORD'),
        'integrity_salt' => env('JAZZCASH_INTEGRITY_SALT'),
        'return_url' => env('JAZZCASH_RETURN_URL'),
        'sandbox' => env('JAZZCASH_SANDBOX', true),
        'endpoint' => env('JAZZCASH_SANDBOX', true)
            ? 'https://sandbox.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform/'
            : 'https://payments.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform/',
    ],

    'bank' => [
        'name' => env('BANK_NAME', 'Meezan Bank'),
        'account_title' => env('BANK_ACCOUNT_TITLE', 'MotoModz'),
        'account_number' => env('BANK_ACCOUNT_NUMBER', '01234567890123'),
        'iban' => env('BANK_IBAN', 'PK00MEZN0001234567890123'),
        'branch' => env('BANK_BRANCH', 'Main Branch, Karachi'),
    ],

    'methods' => [
        'stripe' => 'Credit / Debit Card (Stripe)',
        'jazzcash' => 'JazzCash Mobile Wallet',
        'bank_transfer' => 'Direct Bank Transfer',
        'cod' => 'Cash on Delivery',
    ],
];
