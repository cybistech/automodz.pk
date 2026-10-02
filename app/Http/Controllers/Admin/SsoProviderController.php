<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SsoProvider;
use App\Services\SsoProviderService;
use Illuminate\Http\Request;

class SsoProviderController extends Controller
{
    public function __construct(private SsoProviderService $sso) {}

    public function edit()
    {
        $this->sso->ensureSeeded();

        return view('admin.sso-providers.edit', [
            'providers' => $this->sso->allOrdered(),
            'definitions' => $this->sso->definitions(),
        ]);
    }

    public function update(Request $request)
    {
        $this->sso->ensureSeeded();

        $keys = array_keys($this->sso->definitions());

        $validated = $request->validate([
            'providers' => ['required', 'array'],
            'providers.*.is_enabled' => ['nullable', 'boolean'],
            'providers.*.client_id' => ['nullable', 'string', 'max:500'],
            'providers.*.client_secret' => ['nullable', 'string', 'max:500'],
            'providers.*.redirect_uri' => ['nullable', 'url', 'max:500'],
        ]);

        foreach ($keys as $key) {
            $input = $validated['providers'][$key] ?? [];
            /** @var SsoProvider $provider */
            $provider = SsoProvider::where('key', $key)->firstOrFail();

            $enabled = ! empty($input['is_enabled']);
            $clientId = trim((string) ($input['client_id'] ?? ''));
            $clientSecret = trim((string) ($input['client_secret'] ?? ''));
            $redirectUri = trim((string) ($input['redirect_uri'] ?? ''));

            if ($enabled && ($clientId === '' || ($clientSecret === '' && ! $provider->client_secret))) {
                return back()
                    ->withInput()
                    ->with('warning', "{$provider->label} requires Client ID and Client Secret when enabled.");
            }

            if ($redirectUri !== '' && ! str_contains($redirectUri, '/auth/'.$key.'/callback')) {
                return back()
                    ->withInput()
                    ->with('warning', "Redirect URI for {$provider->label} should point to the callback URL shown below.");
            }

            $provider->fill([
                'is_enabled' => $enabled,
                'client_id' => $clientId !== '' ? $clientId : null,
                'redirect_uri' => $redirectUri !== '' ? $redirectUri : null,
            ]);

            if ($clientSecret !== '') {
                $provider->client_secret = $clientSecret;
            }

            $provider->save();
        }

        return redirect()
            ->route('admin.sso-providers.edit')
            ->with('success', 'SSO login settings saved.');
    }
}
