<section
    x-data="{ open: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }} }"
    class="space-y-5"
>
    <header>
        <h2 class="font-display text-lg font-bold text-red-300">Delete Account</h2>
        <p class="mt-1 text-sm text-slate-400">
            Once deleted, your account and related data are permanently removed. This cannot be undone.
        </p>
    </header>

    <button type="button" class="rounded-xl border border-red-500/40 bg-red-500/10 px-5 py-2.5 text-sm font-bold text-red-300 transition hover:bg-red-500/20" x-on:click="open = true">
        Delete account
    </button>

    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 p-4"
        x-on:keydown.escape.window="open = false"
    >
        <div class="card w-full max-w-md border-red-500/30 p-6" @click.outside="open = false">
            <h3 class="text-lg font-bold text-white">Confirm account deletion</h3>
            <p class="mt-2 text-sm text-slate-400">Enter your password to permanently delete your account.</p>

            <form method="post" action="{{ route('profile.destroy') }}" class="mt-6 space-y-4">
                @csrf
                @method('delete')

                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" placeholder="Password" class="input-field" required>
                    @error('password', 'userDeletion')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" class="btn-secondary" x-on:click="open = false">Cancel</button>
                    <button type="submit" class="rounded-xl border border-red-500/40 bg-red-500/20 px-5 py-2.5 text-sm font-bold text-red-200 transition hover:bg-red-500/30">
                        Delete account
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
