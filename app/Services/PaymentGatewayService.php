<?php

namespace App\Services;

use App\Models\PaymentGateway;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

class PaymentGatewayService
{
    /** @return array<string, array<string, mixed>> */
    public function definitions(): array
    {
        return config('payments.gateways', []);
    }

    public function definition(string $key): ?array
    {
        return $this->definitions()[$key] ?? null;
    }

    /** @return Collection<int, PaymentGateway> */
    public function allOrdered(): Collection
    {
        return PaymentGateway::query()
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get();
    }

    /** @return list<string> */
    public function enabledKeys(): array
    {
        return $this->allOrdered()
            ->filter(fn (PaymentGateway $gateway) => $this->isReady($gateway))
            ->pluck('key')
            ->values()
            ->all();
    }

    /** @return array<string, string> */
    public function checkoutMethodLabels(): array
    {
        $labels = [];

        foreach ($this->allOrdered() as $gateway) {
            if ($this->isReady($gateway)) {
                $labels[$gateway->key] = $gateway->label;
            }
        }

        return $labels;
    }

    public function findByKey(string $key): ?PaymentGateway
    {
        return PaymentGateway::where('key', $key)->first();
    }

    public function isReady(PaymentGateway $gateway): bool
    {
        if (! $gateway->is_enabled) {
            return false;
        }

        $definition = $this->definition($gateway->key);
        if (! $definition) {
            return false;
        }

        foreach ($definition['fields'] as $fieldKey => $field) {
            if (empty($field['required'])) {
                continue;
            }

            if (blank($gateway->setting($fieldKey))) {
                return false;
            }
        }

        return true;
    }

    public function isCheckoutEnabled(string $key): bool
    {
        $gateway = $this->findByKey($key);

        return $gateway ? $this->isReady($gateway) : false;
    }

    public function applyRuntimeConfig(?string $key = null): void
    {
        $gateways = $key
            ? $this->allOrdered()->where('key', $key)
            : $this->allOrdered();

        foreach ($gateways as $gateway) {
            $this->pushGatewayConfig($gateway);
        }

        Config::set('payments.methods', $this->checkoutMethodLabels());
    }

    public function ensureSeeded(): void
    {
        if (PaymentGateway::query()->exists()) {
            return;
        }

        $order = 0;
        foreach ($this->definitions() as $key => $definition) {
            PaymentGateway::create([
                'key' => $key,
                'label' => $definition['label'],
                'is_enabled' => $key === 'cod',
                'settings' => $this->defaultSettingsFor($key),
                'sort_order' => $order++,
            ]);
        }
    }

    /** @return array<string, mixed> */
    private function defaultSettingsFor(string $key): array
    {
        return match ($key) {
            'easypaisa' => [
                'account_title' => config('payments.easypaisa.account_title'),
                'account_number' => config('payments.easypaisa.account_number'),
            ],
            'jazzcash' => [
                'merchant_id' => config('payments.jazzcash.merchant_id'),
                'password' => config('payments.jazzcash.password'),
                'integrity_salt' => config('payments.jazzcash.integrity_salt'),
                'endpoint' => config('payments.jazzcash.endpoint'),
                'return_url' => config('payments.jazzcash.return_url'),
            ],
            'stripe' => [
                'publishable_key' => config('payments.stripe.key'),
                'secret_key' => config('payments.stripe.secret'),
            ],
            'paypal' => [
                'client_id' => config('payments.paypal.client_id'),
                'client_secret' => config('payments.paypal.client_secret'),
                'mode' => config('payments.paypal.mode', 'sandbox'),
            ],
            default => [],
        };
    }

    private function pushGatewayConfig(PaymentGateway $gateway): void
    {
        $settings = $gateway->settings ?? [];

        match ($gateway->key) {
            'easypaisa' => Config::set('payments.easypaisa', [
                'account_title' => $settings['account_title'] ?? '',
                'account_number' => $settings['account_number'] ?? '',
            ]),
            'jazzcash' => Config::set('payments.jazzcash', [
                'merchant_id' => $settings['merchant_id'] ?? '',
                'password' => $settings['password'] ?? '',
                'integrity_salt' => $settings['integrity_salt'] ?? '',
                'endpoint' => $settings['endpoint'] ?? '',
                'return_url' => $settings['return_url'] ?? '',
            ]),
            'stripe' => Config::set('payments.stripe', [
                'key' => $settings['publishable_key'] ?? '',
                'secret' => $settings['secret_key'] ?? '',
            ]),
            'paypal' => Config::set('payments.paypal', [
                'client_id' => $settings['client_id'] ?? '',
                'client_secret' => $settings['client_secret'] ?? '',
                'mode' => $settings['mode'] ?? 'sandbox',
            ]),
            default => null,
        };
    }
}
