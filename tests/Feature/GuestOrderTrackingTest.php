<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class GuestOrderTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_tracking_url_uses_public_site_url_when_app_url_is_local(): void
    {
        Config::set('app.url', 'http://127.0.0.1:8000');
        Config::set('site.url', 'https://automodz.pk');

        $order = Order::create([
            'order_number' => 'AP-PUBLICURL',
            'guest_token' => 'site-url-token',
            'status' => 'processing',
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'subtotal' => 1000,
            'shipping' => 250,
            'tax' => 0,
            'total' => 1250,
            'currency' => 'PKR',
            'customer_name' => 'Public URL Test',
            'customer_email' => 'public@example.com',
            'customer_phone' => '03001234567',
            'shipping_address' => 'Test Address',
            'shipping_city' => 'Lahore',
        ]);

        $url = $order->publicTrackingUrl();

        $this->assertStringStartsWith('https://automodz.pk/order/track/', $url);
        $this->assertStringContainsString('token=site-url-token', $url);
    }

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

        $this->assertStringContainsString('/order/track/APQRTRACK1', $trackingUrl);
        $this->assertStringContainsString('token=public-tracking-token', $trackingUrl);

        $this->get($trackingUrl)
            ->assertOk()
            ->assertSee('AP-QRTRACK1', false)
            ->assertSee('APQRTRACK1', false)
            ->assertSee('Shipped', false)
            ->assertSee('QR Guest', false);
    }

    public function test_qr_link_works_with_courier_tracking_number(): void
    {
        $order = Order::create([
            'order_number' => 'AP-COURIER1',
            'guest_token' => 'courier-token',
            'tracking_number' => 'LE-998877',
            'status' => 'shipped',
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'subtotal' => 1000,
            'shipping' => 250,
            'tax' => 0,
            'total' => 1250,
            'currency' => 'PKR',
            'customer_name' => 'Courier Test',
            'customer_email' => 'courier@example.com',
            'customer_phone' => '03001234567',
            'shipping_address' => 'Test Address',
            'shipping_city' => 'Lahore',
        ]);

        $trackingUrl = $order->shippingLabelQrUrl();

        $this->assertStringContainsString('/order/track/LE998877', $trackingUrl);

        $this->get($trackingUrl)
            ->assertOk()
            ->assertSee('LE-998877', false)
            ->assertSee('Shipped', false);
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

        $this->get(route('orders.tracking', ['tracking' => $order->scannableTrackingReference(), 'token' => 'wrong-token']))
            ->assertForbidden();
    }
}
