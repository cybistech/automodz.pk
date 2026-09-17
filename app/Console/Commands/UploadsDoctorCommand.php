<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class UploadsDoctorCommand extends Command
{
    protected $signature = 'uploads:doctor';

    protected $description = 'Diagnose image upload and /uploads serving issues on this server';

    public function handle(): int
    {
        $this->info('AutoModz upload diagnostics');
        $this->newLine();

        $issues = 0;

        $issues += $this->checkPhpExtensions();
        $issues += $this->checkPhpLimits();
        $issues += $this->checkStorageWritable();
        $issues += $this->checkUploadsLink();
        $issues += $this->checkHtaccess();
        $issues += $this->checkUserIni();

        $this->newLine();

        if ($issues === 0) {
            $this->info('All checks passed. If uploads still fail, check storage/logs/laravel.log after trying an upload.');

            return self::SUCCESS;
        }

        $this->error("Found {$issues} issue(s). Fix them on the server, then run: ./fix-permissions.sh && php artisan uploads:link --force");

        return self::FAILURE;
    }

    private function checkPhpExtensions(): int
    {
        $this->line('<fg=cyan>PHP extensions</>');

        $issues = 0;

        if (extension_loaded('gd')) {
            $webp = function_exists('imagewebp') ? 'yes' : 'no';
            $this->line("  OK  GD loaded (WebP encode: {$webp})");
        } else {
            $this->line('  WARN GD not loaded — uploads still work but images are stored unoptimized');
        }

        if (function_exists('finfo_open')) {
            $this->line('  OK  fileinfo loaded');
        } else {
            $this->line('  ERR fileinfo missing — MIME detection may fail');
            $issues++;
        }

        $this->newLine();

        return $issues;
    }

    private function checkPhpLimits(): int
    {
        $this->line('<fg=cyan>PHP upload limits</>');

        $sapi = PHP_SAPI;
        $cliUpload = ini_get('upload_max_filesize') ?: 'unknown';
        $cliPost = ini_get('post_max_size') ?: 'unknown';
        $memory = ini_get('memory_limit') ?: 'unknown';

        $this->line("  SAPI                = {$sapi}");
        $this->line("  upload_max_filesize = {$cliUpload}".($sapi === 'cli' ? ' (CLI)' : ''));
        $this->line("  post_max_size       = {$cliPost}".($sapi === 'cli' ? ' (CLI)' : ''));
        $this->line("  memory_limit        = {$memory}");

        $webIni = $this->readUserIniLimits();
        $requiredBytes = max(8 * 1024 * 1024, (int) config('media.max_upload_kb', 8192) * 1024);
        $requiredLabel = round($requiredBytes / 1024 / 1024, 1).'M';

        if ($sapi === 'cli') {
            $this->line('  NOTE CLI limits often differ from PHP-FPM / Apache on cPanel.');
        }

        if ($webIni !== []) {
            $this->line('  .user.ini           = upload_max_filesize '
                .($webIni['upload_max_filesize'] ?? '?')
                .', post_max_size '
                .($webIni['post_max_size'] ?? '?'));
        }

        $effectiveUpload = max(
            $this->iniSizeToBytes($cliUpload),
            $this->iniSizeToBytes($webIni['upload_max_filesize'] ?? '0'),
        );
        $effectivePost = max(
            $this->iniSizeToBytes($cliPost),
            $this->iniSizeToBytes($webIni['post_max_size'] ?? '0'),
        );

        $issues = 0;

        if ($effectiveUpload > 0 && $effectiveUpload < $requiredBytes) {
            $this->line("  ERR Effective upload limit below {$requiredLabel} — browser uploads will be rejected");
            $this->line('      Raise upload_max_filesize in .user.ini or cPanel MultiPHP INI Editor');
            $issues++;
        } elseif ($effectivePost > 0 && $effectivePost < $requiredBytes) {
            $this->line("  ERR Effective post_max_size below {$requiredLabel} — multi-image uploads may fail");
            $this->line('      Raise post_max_size in .user.ini or cPanel MultiPHP INI Editor');
            $issues++;
        } else {
            $this->line("  OK  Effective limits sufficient for {$requiredLabel} product images");
        }

        $this->newLine();

        return $issues;
    }

    /**
     * @return array{upload_max_filesize?: string, post_max_size?: string}
     */
    private function readUserIniLimits(): array
    {
        $userIni = base_path('.user.ini');

        if (! is_file($userIni)) {
            return [];
        }

        $limits = [];
        $lines = file($userIni, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, ';') || str_starts_with($line, '#')) {
                continue;
            }

            if (! str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = array_map('trim', explode('=', $line, 2));
            $key = strtolower($key);

            if (in_array($key, ['upload_max_filesize', 'post_max_size'], true)) {
                $limits[$key] = $value;
            }
        }

        return $limits;
    }

    private function checkStorageWritable(): int
    {
        $this->line('<fg=cyan>Storage writability</>');

        $dirs = [
            'uploads',
            'uploads/products',
            'uploads/products/thumbs',
            'uploads/categories',
            'storage/app/public',
            'storage/app/public/products',
            'storage/app/public/products/thumbs',
            'storage/app/public/categories',
            'storage/framework/sessions',
            'storage/framework/views',
            'storage/framework/cache',
            'storage/framework/temp',
            'storage/logs',
            'bootstrap/cache',
        ];

        $issues = 0;

        foreach ($dirs as $dir) {
            $path = base_path($dir);

            if (! is_dir($path)) {
                $this->line("  ERR {$dir} — directory missing");
                $issues++;

                continue;
            }

            $probe = $path.'/.write-test-'.getmypid();

            if (@file_put_contents($probe, 'ok') === false) {
                $owner = $this->pathOwner($path);
                $perms = substr(sprintf('%o', @fileperms($path) ?: 0), -4);
                $this->line("  ERR {$dir} — not writable (owner={$owner}, perms={$perms})");
                $issues++;
            } else {
                @unlink($probe);
                $this->line("  OK  {$dir}");
            }
        }

        $diskRoot = Storage::disk('public')->path('');
        $diskProbe = rtrim($diskRoot, '/').'/.disk-write-test-'.getmypid();

        if (@file_put_contents($diskProbe, 'ok') === false) {
            $this->line('  ERR Storage::disk(public) cannot write files');
            $issues++;
        } else {
            @unlink($diskProbe);
            $this->line('  OK  Storage public disk write test');
        }

        $this->newLine();

        return $issues;
    }

    private function checkUploadsLink(): int
    {
        $this->line('<fg=cyan>/uploads public path</>');

        $uploadsDir = base_path('uploads');
        $legacyDir = storage_path('app/public');
        $issues = 0;

        if (is_link($uploadsDir)) {
            $this->line('  ERR uploads is a symlink — cPanel often returns 403 on image URLs');
            $this->line('      Run: php artisan uploads:link --force');
            $issues++;
        } elseif (is_dir($uploadsDir)) {
            $fileCount = $this->countFiles($uploadsDir);
            $this->line("  OK  uploads/ is a real directory ({$fileCount} file(s))");
        } elseif (file_exists($uploadsDir)) {
            $this->line('  ERR uploads exists but is not a directory');
            $issues++;
        } else {
            $this->line('  ERR uploads/ directory missing — run: php artisan uploads:link');
            $issues++;
        }

        if (is_dir($legacyDir)) {
            $legacyCount = $this->countFiles($legacyDir);
            if ($legacyCount > 0 && (! is_dir($uploadsDir) || $this->countFiles($uploadsDir) === 0)) {
                $this->line("  WARN storage/app/public still has {$legacyCount} file(s) — run: php artisan uploads:link --force");
                $issues++;
            }
        }

        $this->newLine();

        return $issues;
    }

    private function countFiles(string $directory): int
    {
        if (! is_dir($directory)) {
            return 0;
        }

        $count = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
        );

        foreach ($iterator as $item) {
            if ($item->isFile()) {
                $count++;
            }
        }

        return $count;
    }

    private function checkHtaccess(): int
    {
        $this->line('<fg=cyan>Web server config</>');

        $issues = 0;
        $htaccess = base_path('.htaccess');

        if (is_file($htaccess)) {
            $contents = file_get_contents($htaccess) ?: '';
            $hasMediaRules = str_contains($contents, 'media.php?p=');

            if ($hasMediaRules) {
                $this->line('  OK  .htaccess routes /media/ and /uploads/ through media.php');
            } else {
                $this->line('  WARN .htaccess missing media.php rules — recopy from .htaccess.example');
                $issues++;
            }
        } else {
            $this->line('  ERR .htaccess missing — copy .htaccess.example to .htaccess on the server');
            $issues++;
        }

        if (is_file(base_path('media.php'))) {
            $this->line('  OK  media.php present (serves images on cPanel without /uploads/ 403)');
        } else {
            $this->line('  ERR media.php missing — deploy it to the site root');
            $issues++;
        }

        $this->newLine();

        return $issues;
    }

    private function checkUserIni(): int
    {
        $this->line('<fg=cyan>.user.ini (cPanel / LiteSpeed)</>');

        $userIni = base_path('.user.ini');

        if (! is_file($userIni)) {
            $this->line('  WARN .user.ini not found — upload limits may stay at PHP defaults (often 2M)');

            return 0;
        }

        $contents = file_get_contents($userIni) ?: '';
        $hasUpload = str_contains($contents, 'upload_max_filesize');

        if ($hasUpload) {
            $this->line('  OK  .user.ini present with upload_max_filesize');
        } else {
            $this->line('  WARN .user.ini present but upload_max_filesize not set');
        }

        $this->newLine();

        return 0;
    }

    private function iniSizeToBytes(string $value): int
    {
        $value = trim($value);

        if ($value === '' || $value === '-1') {
            return -1;
        }

        $unit = strtolower(substr($value, -1));
        $number = (float) $value;

        return (int) match ($unit) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => (int) $number,
        };
    }

    private function pathOwner(string $path): string
    {
        if (! function_exists('posix_getpwuid') || ! function_exists('fileowner')) {
            return 'unknown';
        }

        $info = @posix_getpwuid(@fileowner($path));

        return is_array($info) ? ($info['name'] ?? 'unknown') : 'unknown';
    }
}
