<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsAppSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class WhatsAppCloudClient
{
    public function __construct(private WhatsAppSetting $settings) {}

    public static function fromSettings(): self
    {
        return new self(WhatsAppSetting::current());
    }

    public function isReady(): bool
    {
        return $this->settings->isConfigured();
    }

    /** @param  array<string, mixed>  $payload */
    public function send(array $payload): array
    {
        if (! $this->isReady()) {
            throw new RuntimeException('WhatsApp Cloud API is not configured.');
        }

        $payload['messaging_product'] = 'whatsapp';

        $url = sprintf(
            '%s/%s/%s/messages',
            rtrim(config('whatsapp.graph_url'), '/'),
            $this->settings->api_version,
            $this->settings->phone_number_id,
        );

        $response = Http::withToken($this->settings->access_token)
            ->acceptJson()
            ->post($url, $payload);

        if (! $response->successful()) {
            Log::warning('WhatsApp API send failed', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            throw new RuntimeException('WhatsApp message could not be sent.');
        }

        return $response->json();
    }

    public function sendText(string $toE164Digits, string $body): array
    {
        return $this->send([
            'to' => $toE164Digits,
            'type' => 'text',
            'text' => [
                'preview_url' => true,
                'body' => $body,
            ],
        ]);
    }

    /** @param  list<array<string, mixed>>  $components */
    public function sendTemplate(string $toE164Digits, string $templateName, array $components = []): array
    {
        $template = [
            'name' => $templateName,
            'language' => ['code' => $this->settings->default_language],
        ];

        if ($components !== []) {
            $template['components'] = $components;
        }

        return $this->send([
            'to' => $toE164Digits,
            'type' => 'template',
            'template' => $template,
        ]);
    }
}
