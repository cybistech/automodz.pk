<?php

return [

    'currency' => env('PAYMENT_CURRENCY', 'PKR'),

    /*
    |--------------------------------------------------------------------------
    | Checkout gateway definitions (admin-managed credentials)
    |--------------------------------------------------------------------------
    */

    'gateways' => [
        'cod' => [
            'label' => 'Cash on Delivery',
            'description' => 'Customer pays when the order is delivered.',
            'fields' => [],
        ],
        'easypaisa' => [
            'label' => 'EasyPaisa Mobile Wallet',
            'description' => 'Manual transfer to your EasyPaisa merchant/wallet account.',
            'fields' => [
                'account_title' => ['label' => 'Account title', 'type' => 'text', 'required' => true],
                'account_number' => ['label' => 'Account number', 'type' => 'text', 'required' => true],
            ],
        ],
        'jazzcash' => [
            'label' => 'JazzCash',
            'description' => 'Redirect customers to JazzCash hosted checkout.',
            'fields' => [
                'merchant_id' => ['label' => 'Merchant ID', 'type' => 'text', 'required' => true],
                'password' => ['label' => 'Password', 'type' => 'password', 'required' => true, 'secret' => true],
                'integrity_salt' => ['label' => 'Integrity salt', 'type' => 'password', 'required' => true, 'secret' => true],
                'endpoint' => ['label' => 'Payment endpoint URL', 'type' => 'url', 'required' => true],
                'return_url' => ['label' => 'Return URL override', 'type' => 'url', 'required' => false],
            ],
        ],
        'stripe' => [
            'label' => 'Stripe (Cards)',
            'description' => 'Accept debit/credit cards via Stripe Checkout.',
            'fields' => [
                'publishable_key' => ['label' => 'Publishable key', 'type' => 'text', 'required' => false],
                'secret_key' => ['label' => 'Secret key', 'type' => 'password', 'required' => true, 'secret' => true],
            ],
        ],
        'paypal' => [
            'label' => 'PayPal',
            'description' => 'PayPal Checkout (sandbox or live).',
            'fields' => [
                'client_id' => ['label' => 'Client ID', 'type' => 'text', 'required' => true],
                'client_secret' => ['label' => 'Client secret', 'type' => 'password', 'required' => true, 'secret' => true],
                'mode' => ['label' => 'Mode (sandbox or live)', 'type' => 'text', 'required' => true],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Runtime config (merged from admin settings)
    |--------------------------------------------------------------------------
    */

    'methods' => [],

    'easypaisa' => [
        'account_title' => env('EASYPAISA_ACCOUNT_TITLE', 'AutoModz'),
        'account_number' => env('EASYPAISA_ACCOUNT_NUMBER', ''),
    ],

    'jazzcash' => [
        'merchant_id' => env('JAZZCASH_MERCHANT_ID', ''),
        'password' => env('JAZZCASH_PASSWORD', ''),
        'integrity_salt' => env('JAZZCASH_INTEGRITY_SALT', ''),
        'endpoint' => env('JAZZCASH_ENDPOINT', 'https://sandbox.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform/'),
        'return_url' => env('JAZZCASH_RETURN_URL', ''),
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY', ''),
        'secret' => env('STRIPE_SECRET', ''),
    ],

    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID', ''),
        'client_secret' => env('PAYPAL_CLIENT_SECRET', ''),
        'mode' => env('PAYPAL_MODE', 'sandbox'),
    ],

];
