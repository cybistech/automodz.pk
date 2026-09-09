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

        $uploadMax = ini_get('upload_max_filesize') ?: 'unknown';
        $postMax = ini_get('post_max_size') ?: 'unknown';
        $memory = ini_get('memory_limit') ?: 'unknown';

        $sapi = PHP_SAPI;
        $this->line("  SAPI                = {$sapi}");
        $this->line("  upload_max_filesize = {$uploadMax}");
        $this->line("  post_max_size       = {$postMax}");
        $this->line("  memory_limit        = {$memory}");

        if ($sapi === 'cli') {
            $this->line('  NOTE CLI limits may differ from PHP-FPM / Apache — check MultiPHP INI or phpinfo() on the site if uploads fail in the browser only.');
        }

        $uploadBytes = $this->iniSizeToBytes($uploadMax);
        $issues = 0;

        if ($uploadBytes > 0 && $uploadBytes < 8 * 1024 * 1024) {
            $this->line('  ERR Limit below 8M — product images up to 8MB will be rejected');
            $this->line('      Copy .user.ini to the server root or raise limits in PHP-FPM / MultiPHP INI');
            $issues++;
        } else {
            $this->line('  OK  Limits sufficient for 8MB product images');
        }

        $this->newLine();

        return $issues;
    }

    private function checkStorageWritable(): int
    {
        $this->line('<fg=cyan>Storage writability</>');

        $dirs = [
            'storage/app/public',
            'storage/app/public/products',
            'storage/app/public/products/thumbs',
            'storage/app/public/categories',
            'storage/framework/temp',
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

        $link = base_path('uploads');
        $target = storage_path('app/public');
        $issues = 0;

        if (is_link($link)) {
            $resolved = realpath($link);
            $targetResolved = realpath($target);

            if ($resolved && $targetResolved && $resolved === $targetResolved) {
                $this->line("  OK  uploads -> {$target}");
            } else {
                $this->line("  ERR uploads symlink points to wrong target: ".readlink($link));
                $issues++;
            }
        } elseif (is_dir($link)) {
            $empty = count(scandir($link)) <= 2;
            $this->line('  ERR uploads is a real directory'.($empty ? ' (empty)' : ' with files'));
            $this->line('      Run: rm -rf uploads && php artisan uploads:link');
            $issues++;
        } elseif (file_exists($link)) {
            $this->line('  ERR uploads exists but is not a symlink or directory');
            $issues++;
        } else {
            $this->line('  ERR uploads symlink missing — run: php artisan uploads:link');
            $issues++;
        }

        $this->newLine();

        return $issues;
    }

    private function checkHtaccess(): int
    {
        $this->line('<fg=cyan>Web server config</>');

        $issues = 0;
        $htaccess = base_path('.htaccess');

        if (is_file($htaccess)) {
            $contents = file_get_contents($htaccess) ?: '';
            $hasUploadRewrite = str_contains($contents, 'storage/app/public');

            if ($hasUploadRewrite) {
                $this->line('  OK  .htaccess present with /uploads fallback rewrite');
            } else {
                $this->line('  WARN .htaccess present but missing /uploads rewrite — recopy from .htaccess.example');
                $issues++;
            }
        } else {
            $this->line('  ERR .htaccess missing — copy .htaccess.example to .htaccess on the server');
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
