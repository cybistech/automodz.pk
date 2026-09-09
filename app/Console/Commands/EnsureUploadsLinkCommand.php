<?php

namespace App\Console\Commands;

use App\Services\ImageOptimizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class EnsureUploadsLinkCommand extends Command
{
    protected $signature = 'uploads:link {--force : Recreate the uploads symlink if it already exists}';

    protected $description = 'Create the /uploads symlink to storage/app/public so product images are publicly reachable';

    public function handle(): int
    {
        $link = base_path('uploads');
        $target = storage_path('app/public');

        if (! is_dir($target)) {
            File::makeDirectory($target, 0775, true);
        }

        if (is_link($link) || file_exists($link)) {
            if (! $this->option('force')) {
                if (is_link($link) && realpath($link) === realpath($target)) {
                    $this->info('Uploads link already exists: uploads -> storage/app/public');

                    return self::SUCCESS;
                }

                $this->error("Path already exists at {$link}. Re-run with --force to replace it.");

                return self::FAILURE;
            }

            if (is_link($link) || is_file($link)) {
                File::delete($link);
            } elseif (is_dir($link)) {
                $entries = array_diff(scandir($link) ?: [], ['.', '..']);

                if ($entries !== []) {
                    $this->error("{$link} is a real directory with files. Move contents to storage/app/public, then re-run with --force.");

                    return self::FAILURE;
                }

                File::deleteDirectory($link);
                $this->warn('Removed empty uploads/ directory so the symlink can be created.');
            }
        }

        File::link($target, $link);
        $this->info('Created symlink: uploads -> storage/app/public');

        return self::SUCCESS;
    }
}
