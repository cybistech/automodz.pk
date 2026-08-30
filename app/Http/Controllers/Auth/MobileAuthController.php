<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GuestOrderService;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MobileAuthController extends Controller
{
    public function __construct(
        private OtpService $otp,
        private GuestOrderService $guestOrders,
    ) {}

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:10|max:20',
        ]);

        $phone = $this->otp->send($request->phone);

        return back()->with([
            'otp_sent' => true,
            'phone' => $phone,
            'status' => 'Verification code sent to '.$phone,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:10|max:20',
            'code' => 'required|string|size:6',
            'name' => 'nullable|string|max:255',
        ]);

        if (! $this->otp->verify($request->phone, $request->code)) {
            return back()->withInput()->withErrors([
                'code' => 'Invalid or expired verification code.',
            ]);
        }

        $phone = $this->otp->normalizePhone($request->phone);
        $user = User::where('phone', $phone)->first();

        if (! $user) {
            $user = User::create([
                'name' => $request->name ?: 'Customer '.substr($phone, -4),
                'email' => 'mobile_'.preg_replace('/\D/', '', $phone).'@autoparts.local',
                'phone' => $phone,
                'phone_verified_at' => now(),
                'email_verified_at' => now(),
                'password' => Hash::make(Str::random(32)),
            ]);
        } else {
            $user->update(['phone_verified_at' => now()]);
        }

        $this->guestOrders->linkOrdersToUser($user);
        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }
}
