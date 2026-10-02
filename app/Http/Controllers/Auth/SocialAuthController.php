<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use App\Services\GuestOrderService;
use App\Services\SsoProviderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function __construct(
        private GuestOrderService $guestOrders,
        private SsoProviderService $sso,
    ) {}

    public function redirect(Request $request, string $provider)
    {
        if (! $this->sso->isLoginEnabled($provider)) {
            abort(404);
        }

        if ($request->filled('redirect')) {
            session(['url.intended' => $request->query('redirect')]);
        }

        $this->sso->applyRuntimeConfig($provider);
        $driver = $this->sso->socialiteDriver($provider);

        return Socialite::driver($driver)->redirect();
    }

    public function callback(string $provider)
    {
        if (! $this->sso->isLoginEnabled($provider)) {
            abort(404);
        }

        $this->sso->applyRuntimeConfig($provider);
        $driver = $this->sso->socialiteDriver($provider);

        $socialUser = Socialite::driver($driver)->user();

        $account = SocialAccount::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($account) {
            $user = $account->user;
        } else {
            $email = $socialUser->getEmail() ?: $provider.'_'.$socialUser->getId().'@social.automodz.local';

            $user = User::where('email', $email)->first();

            if (! $user) {
                $user = User::create([
                    'name' => $socialUser->getName() ?: 'Customer',
                    'email' => $email,
                    'email_verified_at' => now(),
                    'password' => Hash::make(Str::random(32)),
                    'avatar' => $socialUser->getAvatar(),
                ]);
            }

            SocialAccount::updateOrCreate(
                ['provider' => $provider, 'provider_id' => $socialUser->getId()],
                ['user_id' => $user->id, 'avatar' => $socialUser->getAvatar()]
            );

            if ($socialUser->getAvatar() && ! $user->avatar) {
                $user->update(['avatar' => $socialUser->getAvatar()]);
            }
        }

        $this->guestOrders->linkOrdersToUser($user);
        Auth::login($user, true);

        return redirect()->intended(route('dashboard'));
    }
}
