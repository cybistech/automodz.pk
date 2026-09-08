<?php

namespace App\Console\Commands;

use App\Services\ImageOptimizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RegenerateProductThumbsCommand extends Command
{
    protected $signature = 'uploads:thumbs {--force : Overwrite existing thumbs}';

    protected $description = 'Generate missing product thumbnail images under storage/app/public/products/thumbs';

    public function handle(ImageOptimizer $optimizer): int
    {
        $disk = Storage::disk('public');
        $files = collect($disk->allFiles('products'))
            ->filter(fn (string $path) => ! str_contains($path, '/thumbs/')
                && Str::of($path)->lower()->endsWith(['.webp', '.jpg', '.jpeg', '.png', '.gif']))
            ->values();

        if ($files->isEmpty()) {
            $this->warn('No product images found.');

            return self::SUCCESS;
        }

        $created = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($files as $path) {
            $thumb = 'products/thumbs/'.basename($path);

            if ($disk->exists($thumb) && ! $this->option('force')) {
                $skipped++;

                continue;
            }

            try {
                $optimizer->regenerateThumb($path);
                $created++;
                $this->line("OK  {$thumb}");
            } catch (\Throwable $e) {
                $failed++;
                $this->error("ERR {$path}: ".$e->getMessage());
            }
        }

        $this->newLine();
        $this->info("Thumbs created: {$created}, skipped: {$skipped}, failed: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
