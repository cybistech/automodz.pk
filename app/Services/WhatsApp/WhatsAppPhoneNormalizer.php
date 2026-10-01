<?php

namespace App\Services\WhatsApp;

class WhatsAppPhoneNormalizer
{
    public static function toWhatsAppDigits(?string $phone): ?string
    {
        if ($phone === null || trim($phone) === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);

        if ($digits === null || $digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '92'.substr($digits, 1);
        }

        if (strlen($digits) === 10 && str_starts_with($digits, '3')) {
            $digits = '92'.$digits;
        }

        return $digits;
    }

    public static function matches(string $left, string $right): bool
    {
        $a = self::toWhatsAppDigits($left);
        $b = self::toWhatsAppDigits($right);

        return $a !== null && $b !== null && $a === $b;
    }
}
