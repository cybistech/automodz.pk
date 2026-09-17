<?php

return [

    // Per-file upload limit in KB (validated in admin; capped by PHP upload_max_filesize).
    'max_upload_kb' => (int) env('MEDIA_MAX_UPLOAD_KB', 8192),

    'max_width' => (int) env('MEDIA_MAX_WIDTH', 1200),

    'max_height' => (int) env('MEDIA_MAX_HEIGHT', 1200),

    'thumb_width' => (int) env('MEDIA_THUMB_WIDTH', 480),

    'quality' => (int) env('MEDIA_QUALITY', 78),

    'thumb_quality' => (int) env('MEDIA_THUMB_QUALITY', 75),

    'format' => env('MEDIA_FORMAT', 'webp'),

    'watermark' => [
        'enabled' => env('MEDIA_WATERMARK', true),
        'text' => env('MEDIA_WATERMARK_TEXT', 'AutoModz.pk'),
        'font' => env('MEDIA_WATERMARK_FONT', resource_path('fonts/DejaVuSans.ttf')),
    ],

];
