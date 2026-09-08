<x-guest-layout>
    <h2 class="text-center font-display text-xl font-bold text-white">Welcome to {{ config('site.name') }}</h2>
    <p class="mt-1 text-center text-sm text-slate-400">Sign in with email to track your orders at {{ config('site.domain') }}</p>

    @if(session('status'))
        <div class="mt-4 rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm text-green-300">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-6">
        @csrf
        @if(request('redirect'))
            <input type="hidden" name="redirect" value="{{ request('redirect') }}">
        @endif
        <div>
            <label class="text-sm text-slate-400">Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus class="input-field mt-1" placeholder="you@example.com">
            @error('email')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
        </div>
        <div class="mt-4">
            <label class="text-sm text-slate-400">Password</label>
            <input type="password" name="password" required class="input-field mt-1" placeholder="••••••••">
            @error('password')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
        </div>
        <label class="mt-4 flex items-center gap-2 text-sm text-slate-400">
            <input type="checkbox" name="remember" class="rounded text-orange-500">
            Remember me
        </label>
        <button type="submit" class="btn-primary mt-6 w-full">Sign in with Email</button>
        <div class="mt-4 flex justify-between text-sm">
            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-orange-400 hover:text-orange-300">Forgot password?</a>
            @endif
            <a href="{{ route('register') }}" class="text-slate-400 hover:text-white">Create account</a>
        </div>
    </form>

    <div class="mt-6 border-t border-slate-700 pt-6 text-center">
        <p class="text-sm text-slate-400">Want to shop without an account?</p>
        <a href="{{ route('checkout.index') }}" class="mt-2 inline-block text-sm font-medium text-orange-400 hover:text-orange-300">Continue as Guest →</a>
    </div>
</x-guest-layout>
