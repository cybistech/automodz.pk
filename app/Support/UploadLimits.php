<?php

namespace App\Support;

class UploadLimits
{
    /**
     * Parse PHP ini size strings (e.g. "8M", "512K") into bytes.
     */
    public static function iniToBytes(?string $value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        $value = trim($value);
        $unit = strtolower(substr($value, -1));
        $number = (float) $value;

        return (int) match ($unit) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => (float) $value,
        };
    }

    public static function uploadMaxBytes(): int
    {
        return self::iniToBytes(ini_get('upload_max_filesize') ?: '2M');
    }

    public static function postMaxBytes(): int
    {
        return self::iniToBytes(ini_get('post_max_size') ?: '8M');
    }

    /**
     * Effective per-file upload limit in kilobytes (min of app config, upload_max, and post_max).
     */
    public static function effectiveMaxUploadKb(): int
    {
        $appMaxKb = (int) config('media.max_upload_kb', 15360);
        $serverMaxKb = (int) floor(self::uploadMaxBytes() / 1024);
        $postMaxKb = (int) floor(self::postMaxBytes() / 1024);

        $caps = array_filter([$appMaxKb, $serverMaxKb, $postMaxKb], fn (int $v) => $v > 0);

        return min($caps ?: [$appMaxKb]);
    }

    public static function effectivePostMaxKb(): int
    {
        $postMaxKb = (int) floor(self::postMaxBytes() / 1024);

        return $postMaxKb > 0 ? $postMaxKb : 16384;
    }

    public static function effectiveMaxUploadMb(): float
    {
        return round(self::effectiveMaxUploadKb() / 1024, 1);
    }

    public static function humanUploadMax(): string
    {
        return ini_get('upload_max_filesize') ?: '2M';
    }

    public static function humanPostMax(): string
    {
        return ini_get('post_max_size') ?: '8M';
    }
}
