<?php

namespace App\Services\WhatsApp;

use App\Models\Order;
use App\Models\WhatsAppSetting;
use Illuminate\Support\Facades\Log;

class WhatsAppOrderAutomationService
{
    public function __construct(
        private WhatsAppCloudClient $client,
        private WhatsAppSetting $settings,
    ) {}

    public static function make(): self
    {
        $settings = WhatsAppSetting::current();

        return new self(new WhatsAppCloudClient($settings), $settings);
    }

    public function shouldAutomate(): bool
    {
        return $this->settings->isConfigured();
    }

    public function sendOrderConfirmation(Order $order): void
    {
        if (! $this->shouldAutomate() || ! $this->settings->send_order_confirmation) {
            return;
        }

        if ($order->whatsapp_confirmation_sent_at) {
            return;
        }

        $to = WhatsAppPhoneNormalizer::toWhatsAppDigits($order->customer_phone);
        if (! $to) {
            return;
        }

        try {
            if (filled($this->settings->order_confirmation_template)) {
                $this->client->sendTemplate($to, $this->settings->order_confirmation_template, [
                    [
                        'type' => 'body',
                        'parameters' => [
                            ['type' => 'text', 'text' => $order->customer_name],
                            ['type' => 'text', 'text' => $order->order_number],
                            ['type' => 'text', 'text' => number_format((float) $order->total, 0).' '.$order->currency],
                        ],
                    ],
                ]);
            } else {
                $this->client->sendText($to, $this->buildConfirmationText($order));
            }

            $order->forceFill(['whatsapp_confirmation_sent_at' => now()])->save();
        } catch (\Throwable $e) {
            Log::warning('WhatsApp order confirmation failed', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function sendShippedUpdate(Order $order): void
    {
        if (! $this->shouldAutomate() || ! $this->settings->send_shipped_update) {
            return;
        }

        if ($order->whatsapp_shipped_sent_at) {
            return;
        }

        if ($order->status !== 'shipped') {
            return;
        }

        $to = WhatsAppPhoneNormalizer::toWhatsAppDigits($order->customer_phone);
        if (! $to) {
            return;
        }

        $tracking = $order->displayTrackingNumber();
        $trackUrl = route('orders.confirmation', ['order' => $order, 'token' => $order->guest_token]);

        try {
            if (filled($this->settings->shipping_update_template)) {
                $this->client->sendTemplate($to, $this->settings->shipping_update_template, [
                    [
                        'type' => 'body',
                        'parameters' => [
                            ['type' => 'text', 'text' => $order->order_number],
                            ['type' => 'text', 'text' => $tracking],
                            ['type' => 'text', 'text' => $trackUrl],
                        ],
                    ],
                ]);
            } else {
                $this->client->sendText($to, $this->buildShippedText($order, $tracking, $trackUrl));
            }

            $order->forceFill(['whatsapp_shipped_sent_at' => now()])->save();
        } catch (\Throwable $e) {
            Log::warning('WhatsApp shipped update failed', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function handleInboundMessage(string $from, string $messageBody, ?string $buttonPayload = null): bool
    {
        if (! $this->settings->require_customer_confirm) {
            return false;
        }

        $normalizedFrom = WhatsAppPhoneNormalizer::toWhatsAppDigits($from);
        if (! $normalizedFrom) {
            return false;
        }

        $payload = strtolower(trim($buttonPayload ?: $messageBody));
        $orderNumber = $this->extractOrderNumber($payload);

        $orderQuery = Order::query()
            ->whereNull('whatsapp_confirmed_at')
            ->whereNotNull('whatsapp_confirmation_sent_at')
            ->latest();

        if ($orderNumber) {
            $orderQuery->where('order_number', strtoupper($orderNumber));
        }

        $orders = $orderQuery->limit(20)->get();

        $order = $orders->first(function (Order $candidate) use ($normalizedFrom) {
            return WhatsAppPhoneNormalizer::matches($candidate->customer_phone, $normalizedFrom);
        });

        if (! $order) {
            return false;
        }

        if (! $this->isConfirmationMessage($payload, $order)) {
            return false;
        }

        $order->forceFill([
            'whatsapp_confirmed_at' => now(),
            'status' => in_array($order->status, ['pending', 'confirmed'], true) ? 'processing' : $order->status,
        ])->save();

        try {
            $this->client->sendText(
                $normalizedFrom,
                "Thank you {$order->customer_name}! Order {$order->order_number} is confirmed and being prepared for shipment."
            );
        } catch (\Throwable) {
            //
        }

        return true;
    }

    private function isConfirmationMessage(string $payload, Order $order): bool
    {
        if ($payload === 'confirm_order_'.$order->id) {
            return true;
        }

        $keywords = config('whatsapp.confirm_keywords', ['confirm', 'yes']);

        foreach ($keywords as $keyword) {
            if (str_contains($payload, $keyword)) {
                return true;
            }
        }

        return str_contains($payload, strtolower($order->order_number));
    }

    private function extractOrderNumber(string $payload): ?string
    {
        if (preg_match('/\b([A-Z]{2,5}-\d{8}-\d{4}|[A-Z]{2,5}-[A-Z0-9]{4,12})\b/i', $payload, $matches)) {
            return strtoupper($matches[1]);
        }

        if (preg_match('/\b(AP|AMZ)[A-Z0-9-]{4,20}\b/i', $payload, $matches)) {
            return strtoupper($matches[0]);
        }

        return null;
    }

    private function buildConfirmationText(Order $order): string
    {
        return implode("\n", [
            "Hi {$order->customer_name},",
            '',
            "We received your order *{$order->order_number}* at AutoModz.pk.",
            'Total: *'.number_format((float) $order->total, 0)." {$order->currency}*",
            '',
            'Reply *CONFIRM '.$order->order_number.'* to confirm your order for shipment.',
            '',
            'Track anytime: '.route('orders.confirmation', ['order' => $order, 'token' => $order->guest_token]),
        ]);
    }

    private function buildShippedText(Order $order, string $tracking, string $trackUrl): string
    {
        return implode("\n", [
            "Good news {$order->customer_name}!",
            '',
            "Your order *{$order->order_number}* has been shipped.",
            "Tracking ID: *{$tracking}*",
            '',
            "Track your order: {$trackUrl}",
        ]);
    }
}
