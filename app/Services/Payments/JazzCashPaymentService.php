<?php

namespace App\Services\Payments;

use App\Models\Order;
use Illuminate\Support\Carbon;

class JazzCashPaymentService
{
    public function buildPaymentForm(Order $order): array
    {
        $config = config('payments.jazzcash');
        $amount = (int) round($order->total * 100);
        $txnRef = $order->order_number;
        $txnDateTime = Carbon::now()->format('YmdHis');
        $expiry = Carbon::now()->addHours(3)->format('YmdHis');

        $fields = [
            'pp_Version' => '1.1',
            'pp_TxnType' => 'MWALLET',
            'pp_Language' => 'EN',
            'pp_MerchantID' => $config['merchant_id'],
            'pp_SubMerchantID' => '',
            'pp_Password' => $config['password'],
            'pp_BankID' => 'TBANK',
            'pp_ProductID' => 'RETL',
            'pp_TxnRefNo' => $txnRef,
            'pp_Amount' => $amount,
            'pp_TxnCurrency' => 'PKR',
            'pp_TxnDateTime' => $txnDateTime,
            'pp_BillReference' => 'billRef'.$order->id,
            'pp_Description' => 'MotoModz Order '.$order->order_number,
            'pp_TxnExpiryDateTime' => $expiry,
            'pp_ReturnURL' => $config['return_url'] ?: route('payment.jazzcash.return'),
            'pp_SecureHash' => '',
            'ppmpf_1' => $order->customer_phone,
            'ppmpf_2' => $order->customer_email,
            'ppmpf_3' => $order->customer_name,
            'ppmpf_4' => '',
            'ppmpf_5' => '',
        ];

        $fields['pp_SecureHash'] = $this->generateSecureHash($fields, $config['integrity_salt']);

        return [
            'endpoint' => $config['endpoint'],
            'fields' => $fields,
        ];
    }

    public function verifyResponse(array $response): bool
    {
        $receivedHash = $response['pp_SecureHash'] ?? '';
        unset($response['pp_SecureHash']);

        $calculated = $this->generateSecureHash($response, config('payments.jazzcash.integrity_salt'));

        return hash_equals($calculated, $receivedHash);
    }

    public function isSuccessful(array $response): bool
    {
        return ($response['pp_ResponseCode'] ?? '') === '000';
    }

    private function generateSecureHash(array $fields, string $salt): string
    {
        ksort($fields);
        $values = array_values(array_filter($fields, fn ($value) => $value !== '' && $value !== null));
        array_unshift($values, $salt);

        return strtoupper(hash_hmac('sha256', implode('&', $values), $salt));
    }
}
