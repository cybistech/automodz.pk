<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WhatsAppSettingController extends Controller
{
    public function edit()
    {
        $settings = WhatsAppSetting::current();

        return view('admin.whatsapp.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = WhatsAppSetting::current();

        $data = $request->validate([
            'is_enabled' => ['nullable', 'boolean'],
            'send_order_confirmation' => ['nullable', 'boolean'],
            'require_customer_confirm' => ['nullable', 'boolean'],
            'send_shipped_update' => ['nullable', 'boolean'],
            'phone_number_id' => ['nullable', 'string', 'max:64'],
            'access_token' => ['nullable', 'string', 'max:2000'],
            'verify_token' => ['nullable', 'string', 'max:128'],
            'app_secret' => ['nullable', 'string', 'max:255'],
            'api_version' => ['nullable', 'string', 'max:16'],
            'default_language' => ['nullable', 'string', 'max:10'],
            'order_confirmation_template' => ['nullable', 'string', 'max:128'],
            'shipping_update_template' => ['nullable', 'string', 'max:128'],
        ]);

        $settings->fill([
            'is_enabled' => $request->boolean('is_enabled'),
            'send_order_confirmation' => $request->boolean('send_order_confirmation'),
            'require_customer_confirm' => $request->boolean('require_customer_confirm'),
            'send_shipped_update' => $request->boolean('send_shipped_update'),
            'phone_number_id' => $data['phone_number_id'] ?? null,
            'verify_token' => $data['verify_token'] ?? $settings->verify_token,
            'api_version' => $data['api_version'] ?? 'v21.0',
            'default_language' => $data['default_language'] ?? 'en',
            'order_confirmation_template' => $data['order_confirmation_template'] ?? null,
            'shipping_update_template' => $data['shipping_update_template'] ?? null,
        ]);

        if (filled($data['access_token'] ?? null)) {
            $settings->access_token = $data['access_token'];
        }

        if (filled($data['app_secret'] ?? null)) {
            $settings->app_secret = $data['app_secret'];
        }

        if ($settings->is_enabled && (blank($settings->phone_number_id) || blank($settings->access_token))) {
            return back()
                ->withInput()
                ->with('warning', 'Phone Number ID and Access Token are required when WhatsApp automation is enabled.');
        }

        $settings->save();

        return redirect()
            ->route('admin.whatsapp.edit')
            ->with('success', 'WhatsApp automation settings saved.');
    }

    public function regenerateVerifyToken()
    {
        $settings = WhatsAppSetting::current();
        $settings->update(['verify_token' => Str::random(32)]);

        return back()->with('success', 'Webhook verify token regenerated.');
    }
}
