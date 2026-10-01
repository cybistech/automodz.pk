@props(['redirect' => null])

@if(isset($ssoProviders) && $ssoProviders->isNotEmpty())
    <div {{ $attributes->merge(['class' => 'mt-6']) }}>
        <div class="relative flex items-center gap-3">
            <span class="h-px flex-1 bg-slate-700"></span>
            <span class="text-xs uppercase tracking-wide text-slate-500">Or continue with</span>
            <span class="h-px flex-1 bg-slate-700"></span>
        </div>

        <div class="mt-4 grid gap-2 sm:grid-cols-2">
            @foreach($ssoProviders as $provider)
                @php($buttonLabel = config("sso.providers.{$provider->key}.button", "Continue with {$provider->label}"))
                <a
                    href="{{ route('social.redirect', ['provider' => $provider->key, 'redirect' => $redirect]) }}"
                    class="flex items-center justify-center gap-2 rounded-lg border border-slate-700 bg-slate-900/80 px-4 py-2.5 text-sm font-medium text-slate-100 transition hover:border-orange-500/40 hover:bg-slate-800"
                >
                    <x-social-provider-icon :provider="$provider->key" class="h-4 w-4 shrink-0" />
                    <span>{{ $buttonLabel }}</span>
                </a>
            @endforeach
        </div>
    </div>
@endif
