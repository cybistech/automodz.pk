<?php

return [

    /*
    | Default share image (1200×630 JPG). Used when a page has no primary
    | product/banner image. SVG is avoided — most social crawlers ignore it.
    */
    'default_og_image' => env('SEO_DEFAULT_OG_IMAGE', '/images/og-image.jpg'),

    'default_og_image_width' => 1200,

    'default_og_image_height' => 630,

    'default_og_image_type' => 'image/jpeg',

    'sitemap_cache_minutes' => (int) env('SITEMAP_CACHE_MINUTES', 60),

    'google_site_verification' => env('GOOGLE_SITE_VERIFICATION'),

    'feed_items' => (int) env('SEO_FEED_ITEMS', 50),

    'disallowed_paths' => [
        'admin',
        'cart',
        'checkout',
        'login',
        'register',
        'dashboard',
        'profile',
        'orders',
        'order/confirmation',
        'order/track',
        'payment',
        'auth',
        'forgot-password',
        'reset-password',
        'verify-email',
        'confirm-password',
    ],

    'ai_crawlers' => [
        'GPTBot',
        'ChatGPT-User',
        'OAI-SearchBot',
        'Google-Extended',
        'anthropic-ai',
        'ClaudeBot',
        'Claude-Web',
        'PerplexityBot',
        'Applebot-Extended',
        'Bytespider',
        'cohere-ai',
        'FacebookBot',
        'meta-externalagent',
    ],

    /*
    | Homepage hero slideshow. First entry is the canonical “main banner”
    | used for home Open Graph when no other image is set.
    */
    'hero_slides' => [
        ['src' => '/images/hero/honda-cd.jpg', 'alt' => 'Honda CD local bike mods'],
        ['src' => '/images/hero/suzuki-local.jpg', 'alt' => 'Suzuki motorcycle mods Pakistan'],
        ['src' => '/images/hero/mountain-bike-125.jpg', 'alt' => 'Mountain Bike 125 mods'],
        ['src' => '/images/hero/sportbike.jpg', 'alt' => 'Modified sportbike'],
        ['src' => '/images/hero/sportbike2.jpg', 'alt' => 'Custom sport motorcycle'],
        ['src' => '/images/hero/naked-bike.jpg', 'alt' => 'Modified naked bike'],
        ['src' => '/images/hero/cafe-racer.jpg', 'alt' => 'Cafe racer motorcycle'],
        ['src' => '/images/hero/widebody.jpg', 'alt' => 'Widebody modified car'],
        ['src' => '/images/hero/muscle-car.jpg', 'alt' => 'Modified muscle car'],
        ['src' => '/images/hero/supercar.jpg', 'alt' => 'Modified supercar'],
        ['src' => '/images/hero/sedan-mod.jpg', 'alt' => 'Modified sedan'],
    ],

];
