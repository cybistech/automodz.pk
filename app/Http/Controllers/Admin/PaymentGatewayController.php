<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;

class PaymentGatewayController extends Controller
{
    public function __construct(private PaymentGatewayService $gateways) {}

    public function edit()
    {
        $this->gateways->ensureSeeded();

        return view('admin.payment-gateways.edit', [
            'gateways' => $this->gateways->allOrdered(),
            'definitions' => $this->gateways->definitions(),
        ]);
    }

    public function update(Request $request)
    {
        $this->gateways->ensureSeeded();

        $keys = array_keys($this->gateways->definitions());

        $validated = $request->validate([
            'gateways' => ['required', 'array'],
            'gateways.*.is_enabled' => ['nullable', 'boolean'],
            'gateways.*.settings' => ['nullable', 'array'],
            'gateways.*.settings.*' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($keys as $key) {
            $input = $validated['gateways'][$key] ?? [];
            /** @var PaymentGateway $gateway */
            $gateway = PaymentGateway::where('key', $key)->firstOrFail();
            $definition = $this->gateways->definition($key) ?? ['fields' => []];

            $enabled = ! empty($input['is_enabled']);
            $incomingSettings = $input['settings'] ?? [];
            $mergedSettings = $gateway->settings ?? [];

            foreach ($definition['fields'] as $fieldKey => $field) {
                $value = trim((string) ($incomingSettings[$fieldKey] ?? ''));

                if ($value !== '') {
                    $mergedSettings[$fieldKey] = $value;
                } elseif (! empty($field['secret'])) {
                    // keep existing secret when left blank
                } else {
                    $mergedSettings[$fieldKey] = $value;
                }
            }

            if ($enabled) {
                foreach ($definition['fields'] as $fieldKey => $field) {
                    if (empty($field['required'])) {
                        continue;
                    }

                    if (blank($mergedSettings[$fieldKey] ?? null)) {
                        return back()
                            ->withInput()
                            ->with('warning', "{$gateway->label} requires {$field['label']} when enabled.");
                    }
                }
            }

            $gateway->update([
                'is_enabled' => $enabled,
                'settings' => $mergedSettings,
            ]);
        }

        return redirect()
            ->route('admin.payment-gateways.edit')
            ->with('success', 'Payment gateway settings saved.');
    }
}
