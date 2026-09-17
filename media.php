<?php

declare(strict_types=1);

/**
 * Lightweight public media server for cPanel hosts that return 403 on /uploads/.
 * Serves files from uploads/ and storage/app/public/ without Laravel bootstrap.
 */

$path = $_GET['p'] ?? '';

if ($path === '' && isset($_SERVER['PATH_INFO'])) {
    $path = ltrim((string) $_SERVER['PATH_INFO'], '/');
}

$path = str_replace('\\', '/', $path);
$path = ltrim($path, '/');

if ($path === '' || str_contains($path, '..')) {
    http_response_code(404);
    exit('Not found');
}

$roots = [
    __DIR__.'/uploads/',
    __DIR__.'/storage/app/public/',
];

foreach ($roots as $root) {
    $rootReal = realpath($root);

    if ($rootReal === false) {
        continue;
    }

    $full = $rootReal.'/'. $path;
    $fileReal = is_file($full) ? realpath($full) : false;

    if ($fileReal === false || ! str_starts_with($fileReal, $rootReal.DIRECTORY_SEPARATOR)) {
        continue;
    }

    $extension = strtolower(pathinfo($fileReal, PATHINFO_EXTENSION));

    $mime = match ($extension) {
        'webp' => 'image/webp',
        'jpg', 'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'mp4' => 'video/mp4',
        'webm' => 'video/webm',
        default => 'application/octet-stream',
    };

    header('Content-Type: '.$mime);
    header('Content-Length: '.filesize($fileReal));
    header('Cache-Control: public, max-age=31536000, immutable');
    header('X-Content-Type-Options: nosniff');

    readfile($fileReal);
    exit;
}

http_response_code(404);
exit('Not found');
