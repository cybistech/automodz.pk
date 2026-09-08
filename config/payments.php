<?php

return [
    'currency' => env('PAYMENT_CURRENCY', 'PKR'),

    'easypaisa' => [
        'account_title' => env('EASYPAISA_ACCOUNT_TITLE', 'AutoModz'),
        'account_number' => env('EASYPAISA_ACCOUNT_NUMBER', ''),
    ],

    'methods' => [
        'easypaisa' => 'EasyPaisa Mobile Wallet',
        'cod' => 'Cash on Delivery',
    ],
];
