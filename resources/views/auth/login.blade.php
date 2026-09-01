<x-guest-layout>
    <h2 class="text-center text-xl font-bold text-white">Welcome to MotoModz</h2>
    <p class="mt-1 text-center text-sm text-slate-400">Sign in to track your motorcycle parts orders</p>

    @if(session('status'))
        <div class="mt-4 rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm text-green-300">{{ session('status') }}</div>
    @endif

    <div class="mt-6 flex rounded-lg border border-slate-700 p-1" id="auth-tabs">
        <button type="button" data-tab="email" class="auth-tab flex-1 rounded-md px-3 py-2 text-sm font-medium bg-orange-500 text-white">Email</button>
        <button type="button" data-tab="mobile" class="auth-tab flex-1 rounded-md px-3 py-2 text-sm font-medium text-slate-400 hover:text-white">Mobile</button>
        <button type="button" data-tab="sso" class="auth-tab flex-1 rounded-md px-3 py-2 text-sm font-medium text-slate-400 hover:text-white">SSO</button>
    </div>

    {{-- Email Login --}}
    <div id="tab-email" class="auth-panel mt-6">
        <form method="POST" action="{{ route('login') }}">
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
    </div>

    {{-- Mobile OTP Login --}}
    <div id="tab-mobile" class="auth-panel mt-6 hidden">
        @if(!session('otp_sent'))
            <form method="POST" action="{{ route('login.mobile.send') }}">
                @csrf
                <div>
                    <label class="text-sm text-slate-400">Mobile Number</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required class="input-field mt-1" placeholder="03001234567 or +923001234567">
                    @error('phone')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>
                <p class="mt-2 text-xs text-slate-500">We'll send a 6-digit verification code via SMS.</p>
                <button type="submit" class="btn-primary mt-6 w-full">Send Verification Code</button>
            </form>
        @else
            <form method="POST" action="{{ route('login.mobile.verify') }}">
                @csrf
                <input type="hidden" name="phone" value="{{ session('phone') }}">
                <p class="text-sm text-green-400">Code sent to {{ session('phone') }}</p>
                <div class="mt-4">
                    <label class="text-sm text-slate-400">Verification Code</label>
                    <input type="text" name="code" maxlength="6" required class="input-field mt-1 text-center text-2xl tracking-widest" placeholder="000000">
                    @error('code')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>
                <div class="mt-4">
                    <label class="text-sm text-slate-400">Your Name (optional)</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="input-field mt-1" placeholder="For new accounts">
                </div>
                <button type="submit" class="btn-primary mt-6 w-full">Verify & Sign In</button>
                <a href="{{ route('login') }}" class="mt-3 block text-center text-sm text-orange-400">Use a different number</a>
            </form>
        @endif
    </div>

    {{-- SSO Login --}}
    <div id="tab-sso" class="auth-panel mt-6 hidden">
        <p class="text-sm text-slate-400 text-center">Sign in quickly with your social account</p>
        <div class="mt-6 space-y-3">
            <a href="{{ route('social.redirect', 'google') }}" class="flex w-full items-center justify-center gap-3 rounded-lg border border-slate-600 bg-white px-4 py-3 text-sm font-medium text-slate-900 transition hover:bg-slate-100">
                <svg class="h-5 w-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                Continue with Google
            </a>
            <a href="{{ route('social.redirect', 'facebook') }}" class="flex w-full items-center justify-center gap-3 rounded-lg border border-slate-600 bg-[#1877F2] px-4 py-3 text-sm font-medium text-white transition hover:bg-[#166fe5]">
                <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                Continue with Facebook
            </a>
        </div>
        <p class="mt-4 text-center text-xs text-slate-500">Configure Google/Facebook credentials in .env to enable SSO</p>
    </div>

    <div class="mt-6 border-t border-slate-700 pt-6 text-center">
        <p class="text-sm text-slate-400">Want to shop without an account?</p>
        <a href="{{ route('checkout.index') }}" class="mt-2 inline-block text-sm font-medium text-orange-400 hover:text-orange-300">Continue as Guest →</a>
    </div>

    <script>
    document.querySelectorAll('.auth-tab').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.auth-tab').forEach(b => {
                b.classList.remove('bg-orange-500', 'text-white');
                b.classList.add('text-slate-400');
            });
            btn.classList.add('bg-orange-500', 'text-white');
            btn.classList.remove('text-slate-400');
            document.querySelectorAll('.auth-panel').forEach(p => p.classList.add('hidden'));
            document.getElementById('tab-' + btn.dataset.tab).classList.remove('hidden');
        });
    });
    @if(session('otp_sent'))
        document.querySelector('[data-tab="mobile"]').click();
    @endif
    </script>
</x-guest-layout>
