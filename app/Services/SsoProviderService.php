<?php

namespace App\Services;

use App\Models\SsoProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

class SsoProviderService
{
    /** @return array<string, array{label: string, driver: string, button: string}> */
    public function definitions(): array
    {
        return config('sso.providers', []);
    }

    public function definition(string $key): ?array
    {
        return $this->definitions()[$key] ?? null;
    }

    public function socialiteDriver(string $key): string
    {
        $definition = $this->definition($key);

        if (! $definition) {
            throw new InvalidArgumentException("Unknown SSO provider [{$key}].");
        }

        return $definition['driver'];
    }

    /** @return Collection<int, SsoProvider> */
    public function allOrdered(): Collection
    {
        return SsoProvider::query()
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get();
    }

    /** @return Collection<int, SsoProvider> */
    public function enabledForLogin(): Collection
    {
        return $this->allOrdered()
            ->filter(fn (SsoProvider $provider) => $provider->isReady());
    }

    public function findByKey(string $key): ?SsoProvider
    {
        return SsoProvider::where('key', $key)->first();
    }

    public function isLoginEnabled(string $key): bool
    {
        $provider = $this->findByKey($key);

        return $provider?->isReady() ?? false;
    }

    public function applyRuntimeConfig(string $key): void
    {
        $provider = $this->findByKey($key);

        if (! $provider?->isReady()) {
            abort(404);
        }

        $driver = $this->socialiteDriver($key);

        Config::set("services.{$driver}", [
            'client_id' => $provider->client_id,
            'client_secret' => $provider->client_secret,
            'redirect' => $provider->callbackUrl(),
        ]);
    }

    public function ensureSeeded(): void
    {
        if (SsoProvider::query()->exists()) {
            return;
        }

        $order = 0;
        foreach ($this->definitions() as $key => $definition) {
            SsoProvider::create([
                'key' => $key,
                'label' => $definition['label'],
                'is_enabled' => false,
                'sort_order' => $order++,
            ]);
        }
    }
}
