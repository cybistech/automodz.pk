<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$tempDir = __DIR__.'/storage/framework/temp';
if (! is_dir($tempDir)) {
    @mkdir($tempDir, 0775, true);
}

if (is_dir($tempDir) && is_writable($tempDir)) {
    putenv('TMPDIR='.$tempDir);
    $_ENV['TMPDIR'] = $tempDir;
    $_SERVER['TMPDIR'] = $tempDir;
}

if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
