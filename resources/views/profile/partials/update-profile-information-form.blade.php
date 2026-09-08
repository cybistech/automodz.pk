<section>
    <header>
        <h2 class="font-display text-lg font-bold text-white">Profile Information</h2>
        <p class="mt-1 text-sm text-slate-400">Update your name, contact details, and shipping defaults.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="text-sm text-slate-400">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" class="input-field mt-1">
            @error('name')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email" class="text-sm text-slate-400">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" class="input-field mt-1">
            @error('email')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <p class="mt-2 text-sm text-yellow-300">
                    Your email address is unverified.
                    <button form="send-verification" class="ml-1 font-semibold text-orange-400 underline hover:text-orange-300">
                        Resend verification email
                    </button>
                </p>
                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 text-sm font-medium text-green-400">A new verification link has been sent.</p>
                @endif
            @endif
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="phone" class="text-sm text-slate-400">Phone</label>
                <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}" autocomplete="tel" class="input-field mt-1" placeholder="+92 300 1234567">
                @error('phone')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="city" class="text-sm text-slate-400">City</label>
                <input id="city" name="city" type="text" value="{{ old('city', $user->city) }}" autocomplete="address-level2" class="input-field mt-1" placeholder="Lahore">
                @error('city')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label for="address" class="text-sm text-slate-400">Address</label>
            <textarea id="address" name="address" rows="2" class="input-field mt-1" placeholder="Street, area, landmark">{{ old('address', $user->address) }}</textarea>
            @error('address')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center gap-4 pt-1">
            <button type="submit" class="btn-primary">Save profile</button>
        </div>
    </form>
</section>
