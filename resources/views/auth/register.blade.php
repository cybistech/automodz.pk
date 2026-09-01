<x-guest-layout>
    <h2 class="text-center text-xl font-bold text-white">Create Account</h2>
    <p class="mt-1 text-center text-sm text-slate-400">Register with email to track orders easily</p>

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label class="text-sm text-slate-400">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus class="input-field mt-1">
            @error('name')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="text-sm text-slate-400">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="input-field mt-1">
            @error('email')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="text-sm text-slate-400">Mobile Number (optional)</label>
            <input type="tel" name="phone" value="{{ old('phone') }}" class="input-field mt-1" placeholder="03001234567">
            @error('phone')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="text-sm text-slate-400">Password</label>
            <input type="password" name="password" required class="input-field mt-1">
            @error('password')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="text-sm text-slate-400">Confirm Password</label>
            <input type="password" name="password_confirmation" required class="input-field mt-1">
        </div>
        <button type="submit" class="btn-primary w-full">Create Account</button>
        <p class="text-center text-sm text-slate-400">
            Already have an account? <a href="{{ route('login') }}" class="text-orange-400 hover:text-orange-300">Sign in</a>
        </p>
        <p class="text-center text-sm text-slate-500">
            Or <a href="{{ route('checkout.index') }}" class="text-orange-400 hover:text-orange-300">checkout as guest</a>
        </p>
    </form>
</x-guest-layout>
