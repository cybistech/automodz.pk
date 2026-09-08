<?php

return [

    'sitemap_cache_minutes' => (int) env('SITEMAP_CACHE_MINUTES', 60),

    'google_site_verification' => env('GOOGLE_SITE_VERIFICATION'),

    'default_og_image' => env('SEO_DEFAULT_OG_IMAGE', '/images/logo.svg'),

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

];
