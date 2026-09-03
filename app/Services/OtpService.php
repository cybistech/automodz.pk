<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Support\ResilientCache;
use Illuminate\Support\Facades\Log;

class OtpService
{
    public function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (str_starts_with($digits, '92')) {
            return '+'.$digits;
        }

        if (str_starts_with($digits, '0')) {
            return '+92'.substr($digits, 1);
        }

        return '+92'.$digits;
    }

    public function send(string $phone): string
    {
        $phone = $this->normalizePhone($phone);
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $stored = ResilientCache::put($this->cacheKey($phone), $code, now()->addMinutes(5));

        if (! $stored) {
            session([$this->sessionKey($phone) => $code]);
        }

        Log::info('OTP sent', ['phone' => $phone, 'code' => $code]);

        return $phone;
    }

    public function verify(string $phone, string $code): bool
    {
        $phone = $this->normalizePhone($phone);
        $stored = ResilientCache::get($this->cacheKey($phone))
            ?? session($this->sessionKey($phone));

        if (! $stored || $stored !== $code) {
            return false;
        }

        ResilientCache::forget($this->cacheKey($phone));
        session()->forget($this->sessionKey($phone));

        return true;
    }

    private function cacheKey(string $phone): string
    {
        return 'otp:'.md5($phone);
    }

    private function sessionKey(string $phone): string
    {
        return 'otp_session:'.md5($phone);
    }
}
