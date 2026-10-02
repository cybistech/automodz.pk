<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestOrderTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_public_tracking_link_from_qr_code(): void
    {
        $order = Order::create([
            'order_number' => 'AP-QRTRACK1',
            'guest_token' => 'public-tracking-token',
            'status' => 'shipped',
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'subtotal' => 1000,
            'shipping' => 250,
            'tax' => 0,
            'total' => 1250,
            'currency' => 'PKR',
            'customer_name' => 'QR Guest',
            'customer_email' => 'qr@example.com',
            'customer_phone' => '03001234567',
            'shipping_address' => 'Test Address',
            'shipping_city' => 'Lahore',
        ]);

        $trackingUrl = $order->shippingLabelQrUrl();

        $this->assertStringContainsString('/order/'.$order->id.'/tracking', $trackingUrl);
        $this->assertStringContainsString('token=public-tracking-token', $trackingUrl);

        $this->get($trackingUrl)
            ->assertOk()
            ->assertSee('AP-QRTRACK1', false)
            ->assertSee('QR Guest', false);
    }

    public function test_invalid_tracking_token_is_rejected(): void
    {
        $order = Order::create([
            'order_number' => 'AP-BADTOKEN',
            'guest_token' => 'valid-token',
            'status' => 'processing',
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'subtotal' => 1000,
            'shipping' => 250,
            'tax' => 0,
            'total' => 1250,
            'currency' => 'PKR',
            'customer_name' => 'Test User',
            'customer_email' => 'test@example.com',
            'customer_phone' => '03001234567',
            'shipping_address' => 'Test Address',
            'shipping_city' => 'Lahore',
        ]);

        $this->get(route('orders.tracking', ['order' => $order, 'token' => 'wrong-token']))
            ->assertForbidden();
    }
}
