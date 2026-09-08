<section>
    <header>
        <h2 class="font-display text-lg font-bold text-white">Update Password</h2>
        <p class="mt-1 text-sm text-slate-400">Use a long, unique password to keep your account secure.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="text-sm text-slate-400">Current password</label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" class="input-field mt-1">
            @error('current_password', 'updatePassword')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="update_password_password" class="text-sm text-slate-400">New password</label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password" class="input-field mt-1">
            @error('password', 'updatePassword')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="update_password_password_confirmation" class="text-sm text-slate-400">Confirm password</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="input-field mt-1">
            @error('password_confirmation', 'updatePassword')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
        </div>

        <div class="pt-1">
            <button type="submit" class="btn-primary">Save password</button>
        </div>
    </form>
</section>
