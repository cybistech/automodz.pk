<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WhatsAppSetting extends Model
{
    protected $table = 'whatsapp_settings';

    protected $fillable = [
        'is_enabled',
        'send_order_confirmation',
        'require_customer_confirm',
        'send_shipped_update',
        'phone_number_id',
        'access_token',
        'verify_token',
        'app_secret',
        'api_version',
        'default_language',
        'order_confirmation_template',
        'shipping_update_template',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'send_order_confirmation' => 'boolean',
            'require_customer_confirm' => 'boolean',
            'send_shipped_update' => 'boolean',
            'access_token' => 'encrypted',
            'app_secret' => 'encrypted',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'verify_token' => Str::random(32),
            'is_enabled' => false,
        ]);
    }

    public function isConfigured(): bool
    {
        return $this->is_enabled
            && filled($this->phone_number_id)
            && filled($this->access_token);
    }

    public function webhookUrl(): string
    {
        return url('/webhooks/whatsapp');
    }
}
