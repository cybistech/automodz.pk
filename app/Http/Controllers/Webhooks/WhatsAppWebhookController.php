<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppSetting;
use App\Services\WhatsApp\WhatsAppOrderAutomationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function verify(Request $request)
    {
        $settings = WhatsAppSetting::current();

        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $token && hash_equals((string) $settings->verify_token, (string) $token)) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        return response('Forbidden', 403);
    }

    public function handle(Request $request)
    {
        $automation = WhatsAppOrderAutomationService::make();
        $settings = WhatsAppSetting::current();

        if (filled($settings->app_secret)) {
            $signature = $request->header('X-Hub-Signature-256');
            $expected = 'sha256='.hash_hmac('sha256', $request->getContent(), $settings->app_secret);

            if (! $signature || ! hash_equals($expected, $signature)) {
                Log::warning('WhatsApp webhook signature mismatch');

                return response()->json(['status' => 'invalid signature'], 403);
            }
        }

        $payload = $request->all();

        foreach (data_get($payload, 'entry', []) as $entry) {
            foreach (data_get($entry, 'changes', []) as $change) {
                $value = data_get($change, 'value', []);
                foreach (data_get($value, 'messages', []) as $message) {
                    $from = (string) data_get($message, 'from', '');
                    $type = (string) data_get($message, 'type', '');

                    $body = '';
                    $buttonPayload = null;

                    if ($type === 'text') {
                        $body = (string) data_get($message, 'text.body', '');
                    } elseif ($type === 'button') {
                        $body = (string) data_get($message, 'button.text', '');
                        $buttonPayload = (string) data_get($message, 'button.payload', $body);
                    } elseif ($type === 'interactive') {
                        $buttonPayload = (string) data_get($message, 'interactive.button_reply.id')
                            ?: (string) data_get($message, 'interactive.list_reply.id');
                        $body = (string) data_get($message, 'interactive.button_reply.title')
                            ?: (string) data_get($message, 'interactive.list_reply.title');
                    }

                    if ($from !== '' && ($body !== '' || $buttonPayload)) {
                        $automation->handleInboundMessage($from, $body, $buttonPayload);
                    }
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
