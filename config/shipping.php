<?php

return [

    'origin_city' => env('SHIPPING_ORIGIN_CITY', 'Lahore'),

    'sender' => [
        'name' => env('SHIPPING_SENDER_NAME', 'AutoModz.pk'),
        'address_line_1' => env('SHIPPING_SENDER_ADDRESS_1', '123-A, Main Boulevard, Ichra'),
        'address_line_2' => env('SHIPPING_SENDER_ADDRESS_2', 'Lahore, Punjab, Pakistan'),
        'phone' => env('SHIPPING_SENDER_PHONE', env('SITE_WHATSAPP_DISPLAY', '+92 312 4094997')),
        'email' => env('SHIPPING_SENDER_EMAIL', env('SITE_EMAIL', 'info@automodz.pk')),
    ],

    'receiver_province' => env('SHIPPING_RECEIVER_PROVINCE', 'Punjab'),
    'receiver_country' => env('SHIPPING_RECEIVER_COUNTRY', 'Pakistan'),

];
