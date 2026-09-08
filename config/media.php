<?php

return [

    'max_width' => (int) env('MEDIA_MAX_WIDTH', 1200),

    'max_height' => (int) env('MEDIA_MAX_HEIGHT', 1200),

    'thumb_width' => (int) env('MEDIA_THUMB_WIDTH', 480),

    'quality' => (int) env('MEDIA_QUALITY', 78),

    'thumb_quality' => (int) env('MEDIA_THUMB_QUALITY', 75),

    'format' => env('MEDIA_FORMAT', 'webp'),

];
