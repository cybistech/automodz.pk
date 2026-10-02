<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SsoProvider extends Model
{
    protected $fillable = [
        'key',
        'label',
        'is_enabled',
        'client_id',
        'client_secret',
        'redirect_uri',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'client_secret' => 'encrypted',
            'sort_order' => 'integer',
        ];
    }

    public function callbackUrl(): string
    {
        return $this->redirect_uri ?: route('social.callback', ['provider' => $this->key]);
    }

    public function isReady(): bool
    {
        return $this->is_enabled
            && filled($this->client_id)
            && filled($this->client_secret);
    }
}
