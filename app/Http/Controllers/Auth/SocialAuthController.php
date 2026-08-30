<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use App\Services\GuestOrderService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function __construct(private GuestOrderService $guestOrders) {}

    public function redirect(string $provider)
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        $this->validateProvider($provider);

        $socialUser = Socialite::driver($provider)->user();

        $account = SocialAccount::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($account) {
            $user = $account->user;
        } else {
            $email = $socialUser->getEmail() ?: $provider.'_'.$socialUser->getId().'@social.autoparts.local';

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

    private function validateProvider(string $provider): void
    {
        if (! in_array($provider, ['google', 'facebook'])) {
            abort(404);
        }
    }
}
