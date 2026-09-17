<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class EnsureUploadsLinkCommand extends Command
{
    protected $signature = 'uploads:link {--force : Replace an existing uploads symlink with a real directory}';

    protected $description = 'Prepare the real uploads/ directory and migrate files from storage/app/public';

    public function handle(): int
    {
        $uploadsDir = base_path('uploads');
        $legacyDir = storage_path('app/public');

        File::ensureDirectoryExists($legacyDir, 0775);

        if (is_link($uploadsDir)) {
            if (! $this->option('force')) {
                $this->warn('uploads is a symlink (cPanel often returns 403). Re-run with --force to convert to a real directory.');

                return self::SUCCESS;
            }

            File::delete($uploadsDir);
            $this->warn('Removed uploads symlink.');
        } elseif (is_file($uploadsDir)) {
            $this->error("Cannot use uploads path: {$uploadsDir} is a file.");

            return self::FAILURE;
        }

        File::ensureDirectoryExists($uploadsDir, 0775);

        foreach (['products', 'products/thumbs', 'categories'] as $directory) {
            File::ensureDirectoryExists($uploadsDir.'/'.$directory, 0775);
        }

        $copied = $this->migrateLegacyFiles($legacyDir, $uploadsDir);

        if ($copied > 0) {
            $this->info("Migrated {$copied} file(s) from storage/app/public to uploads/.");
        }

        $this->info('Uploads directory ready: uploads/ (real directory, not a symlink).');

        return self::SUCCESS;
    }

    private function migrateLegacyFiles(string $legacyDir, string $uploadsDir): int
    {
        if (! is_dir($legacyDir)) {
            return 0;
        }

        $copied = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($legacyDir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST,
        );

        foreach ($iterator as $item) {
            /** @var \SplFileInfo $item */
            $relative = substr($item->getPathname(), strlen($legacyDir) + 1);

            if ($relative === false || $relative === '') {
                continue;
            }

            $target = $uploadsDir.'/'.$relative;

            if ($item->isDir()) {
                File::ensureDirectoryExists($target, 0775);

                continue;
            }

            if (is_file($target)) {
                continue;
            }

            File::ensureDirectoryExists(dirname($target), 0775);

            if (@copy($item->getPathname(), $target)) {
                @chmod($target, 0644);
                $copied++;
            }
        }

        return $copied;
    }
}
