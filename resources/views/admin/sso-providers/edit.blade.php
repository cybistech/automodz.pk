@extends('layouts.admin')

@section('title', 'SSO Login Settings')

@section('content')
<div class="max-w-4xl">
    <p class="text-sm text-slate-400">
        Enable social sign-in providers and store OAuth credentials. Only enabled providers with valid credentials appear on the login and registration pages.
    </p>

    <form action="{{ route('admin.sso-providers.update') }}" method="POST" class="mt-6 space-y-6">
        @csrf
        @method('PATCH')

        @foreach($providers as $provider)
            @php($definition = $definitions[$provider->key] ?? [])
            <div class="card p-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-white">{{ $provider->label }}</h2>
                        <p class="mt-1 text-xs text-slate-500">Driver: {{ $definition['driver'] ?? $provider->key }}</p>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-slate-300">
                        <input
                            type="checkbox"
                            name="providers[{{ $provider->key }}][is_enabled]"
                            value="1"
                            class="rounded text-orange-500"
                            @checked(old("providers.{$provider->key}.is_enabled", $provider->is_enabled))
                        >
                        Active
                    </label>
                </div>

                <dl class="mt-4 rounded-lg border border-slate-800 bg-slate-900/60 px-4 py-3 text-xs text-slate-400">
                    <div class="flex flex-wrap gap-x-6 gap-y-1">
                        <div>
                            <dt class="uppercase tracking-wide text-slate-500">Callback URL</dt>
                            <dd class="mt-0.5 font-mono text-slate-300">{{ route('social.callback', ['provider' => $provider->key]) }}</dd>
                        </div>
                    </div>
                </dl>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="text-sm text-slate-400">Client ID</label>
                        <input
                            type="text"
                            name="providers[{{ $provider->key }}][client_id]"
                            value="{{ old("providers.{$provider->key}.client_id", $provider->client_id) }}"
                            class="input-field mt-1 font-mono text-sm"
                            autocomplete="off"
                        >
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-sm text-slate-400">Client Secret</label>
                        <input
                            type="password"
                            name="providers[{{ $provider->key }}][client_secret]"
                            value=""
                            placeholder="{{ $provider->client_secret ? '•••••••• (leave blank to keep current)' : '' }}"
                            class="input-field mt-1 font-mono text-sm"
                            autocomplete="new-password"
                        >
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-sm text-slate-400">Custom redirect URI <span class="text-slate-600">(optional)</span></label>
                        <input
                            type="url"
                            name="providers[{{ $provider->key }}][redirect_uri]"
                            value="{{ old("providers.{$provider->key}.redirect_uri", $provider->redirect_uri) }}"
                            placeholder="{{ route('social.callback', ['provider' => $provider->key]) }}"
                            class="input-field mt-1 font-mono text-sm"
                        >
                    </div>
                </div>
            </div>
        @endforeach

        <button type="submit" class="btn-primary">Save SSO settings</button>
    </form>
</div>
@endsection
