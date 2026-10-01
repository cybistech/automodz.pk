<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\WhatsAppSetting;
use App\Services\PaymentGatewayService;
use App\Services\WhatsApp\WhatsAppOrderAutomationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppOrderAutomationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PaymentGatewayService::class)->ensureSeeded();
    }

    public function test_admin_can_open_whatsapp_settings(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('admin.whatsapp.edit'))
            ->assertOk()
            ->assertSee('WhatsApp automation', false);
    }

    public function test_order_confirmation_sends_whatsapp_message(): void
    {
        Http::fake([
            'graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.test']]]),
        ]);

        WhatsAppSetting::current()->update([
            'is_enabled' => true,
            'phone_number_id' => '123456789',
            'access_token' => 'test-token',
            'send_order_confirmation' => true,
        ]);

        $order = Order::create([
            'order_number' => 'AP-TEST1234',
            'guest_token' => 'token',
            'status' => 'pending',
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
            'shipping_address' => 'Test',
            'shipping_city' => 'Lahore',
        ]);

        WhatsAppOrderAutomationService::make()->sendOrderConfirmation($order);

        $order->refresh();

        $this->assertNotNull($order->whatsapp_confirmation_sent_at);
        Http::assertSentCount(1);
    }

    public function test_inbound_confirm_marks_order_processing(): void
    {
        Http::fake([
            'graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.test']]]),
        ]);

        WhatsAppSetting::current()->update([
            'is_enabled' => true,
            'phone_number_id' => '123456789',
            'access_token' => 'test-token',
            'require_customer_confirm' => true,
        ]);

        $order = Order::create([
            'order_number' => 'AP-CONFIRM1',
            'guest_token' => 'token',
            'status' => 'pending',
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'subtotal' => 1000,
            'shipping' => 250,
            'tax' => 0,
            'total' => 1250,
            'currency' => 'PKR',
            'customer_name' => 'Test User',
            'customer_email' => 'test@example.com',
            'customer_phone' => '923001234567',
            'shipping_address' => 'Test',
            'shipping_city' => 'Lahore',
            'whatsapp_confirmation_sent_at' => now(),
        ]);

        $handled = WhatsAppOrderAutomationService::make()->handleInboundMessage(
            '923001234567',
            'CONFIRM AP-CONFIRM1',
        );

        $this->assertTrue($handled);
        $order->refresh();
        $this->assertNotNull($order->whatsapp_confirmed_at);
        $this->assertSame('processing', $order->status);
    }
}
