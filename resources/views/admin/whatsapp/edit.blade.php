@extends('layouts.admin')

@section('title', 'WhatsApp Automation')

@section('content')
<div class="max-w-4xl space-y-6">
    <p class="text-sm text-slate-400">
        Connect Meta WhatsApp Cloud API to send order confirmations, collect customer confirmation replies, and notify customers when orders ship.
        Full setup guide: <code class="rounded bg-slate-800 px-1.5 py-0.5 text-xs">docs/WHATSAPP_AUTOMATION.md</code>
    </p>

    <div class="card p-6">
        <h2 class="font-semibold text-white">Webhook</h2>
        <dl class="mt-3 space-y-2 text-sm">
            <div>
                <dt class="text-slate-500">Callback URL</dt>
                <dd class="mt-1 font-mono text-slate-300">{{ $settings->webhookUrl() }}</dd>
            </div>
            <div>
                <dt class="text-slate-500">Verify token</dt>
                <dd class="mt-1 font-mono text-slate-300">{{ $settings->verify_token }}</dd>
            </div>
        </dl>
        <form action="{{ route('admin.whatsapp.regenerate-verify-token') }}" method="POST" class="mt-4">
            @csrf
            <button type="submit" class="btn-secondary text-sm">Regenerate verify token</button>
        </form>
    </div>

    <form action="{{ route('admin.whatsapp.update') }}" method="POST" class="card space-y-6 p-6">
        @csrf
        @method('PATCH')

        <div class="grid gap-4 sm:grid-cols-2">
            <label class="flex items-center gap-2 text-sm text-slate-300 sm:col-span-2">
                <input type="checkbox" name="is_enabled" value="1" class="rounded text-orange-500" @checked(old('is_enabled', $settings->is_enabled))>
                Enable WhatsApp automation
            </label>
            <label class="flex items-center gap-2 text-sm text-slate-300">
                <input type="checkbox" name="send_order_confirmation" value="1" class="rounded text-orange-500" @checked(old('send_order_confirmation', $settings->send_order_confirmation))>
                Send order confirmation
            </label>
            <label class="flex items-center gap-2 text-sm text-slate-300">
                <input type="checkbox" name="require_customer_confirm" value="1" class="rounded text-orange-500" @checked(old('require_customer_confirm', $settings->require_customer_confirm))>
                Require customer WhatsApp confirm
            </label>
            <label class="flex items-center gap-2 text-sm text-slate-300">
                <input type="checkbox" name="send_shipped_update" value="1" class="rounded text-orange-500" @checked(old('send_shipped_update', $settings->send_shipped_update))>
                Send shipped / tracking update
            </label>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="text-sm text-slate-400">Phone Number ID</label>
                <input type="text" name="phone_number_id" value="{{ old('phone_number_id', $settings->phone_number_id) }}" class="input-field mt-1 font-mono text-sm">
            </div>
            <div>
                <label class="text-sm text-slate-400">Graph API version</label>
                <input type="text" name="api_version" value="{{ old('api_version', $settings->api_version) }}" class="input-field mt-1 font-mono text-sm">
            </div>
            <div class="sm:col-span-2">
                <label class="text-sm text-slate-400">Permanent access token</label>
                <input type="password" name="access_token" value="" placeholder="{{ $settings->access_token ? '•••••••• (leave blank to keep current)' : '' }}" class="input-field mt-1 font-mono text-sm" autocomplete="new-password">
            </div>
            <div class="sm:col-span-2">
                <label class="text-sm text-slate-400">App secret (recommended for webhook signature verification)</label>
                <input type="password" name="app_secret" value="" placeholder="{{ $settings->app_secret ? '•••••••• (leave blank to keep current)' : '' }}" class="input-field mt-1 font-mono text-sm" autocomplete="new-password">
            </div>
            <div>
                <label class="text-sm text-slate-400">Verify token</label>
                <input type="text" name="verify_token" value="{{ old('verify_token', $settings->verify_token) }}" class="input-field mt-1 font-mono text-sm">
            </div>
            <div>
                <label class="text-sm text-slate-400">Template language code</label>
                <input type="text" name="default_language" value="{{ old('default_language', $settings->default_language) }}" class="input-field mt-1 font-mono text-sm">
            </div>
            <div>
                <label class="text-sm text-slate-400">Order confirmation template name</label>
                <input type="text" name="order_confirmation_template" value="{{ old('order_confirmation_template', $settings->order_confirmation_template) }}" class="input-field mt-1 font-mono text-sm" placeholder="Optional — uses text message if empty">
            </div>
            <div>
                <label class="text-sm text-slate-400">Shipping update template name</label>
                <input type="text" name="shipping_update_template" value="{{ old('shipping_update_template', $settings->shipping_update_template) }}" class="input-field mt-1 font-mono text-sm" placeholder="Optional — uses text message if empty">
            </div>
        </div>

        <button type="submit" class="btn-primary">Save WhatsApp settings</button>
    </form>
</div>
@endsection
