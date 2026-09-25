<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminShippingLabelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_shipping_label(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $order = Order::create([
            'order_number' => 'AMZ-20260925-0427',
            'guest_token' => 'test-guest-token',
            'status' => 'processing',
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'subtotal' => 2600,
            'shipping' => 250,
            'tax' => 0,
            'total' => 2850,
            'currency' => 'PKR',
            'customer_name' => 'Saad Sajid',
            'customer_email' => 'saad@example.com',
            'customer_phone' => '+92 321 9876543',
            'shipping_address' => 'House No. 123, Street 5, Johar Town',
            'shipping_city' => 'Lahore',
        ]);

        $trackingUrl = $order->shippingLabelQrUrl();

        $this->actingAs($admin)
            ->get(route('admin.orders.shipping-label', $order))
            ->assertOk()
            ->assertSee($trackingUrl, false)
            ->assertSee('AMZ-20260925-0427', false)
            ->assertSee('AMZ202609250427', false)
            ->assertSee('Saad Sajid', false)
            ->assertSee('Print shipping label', false)
            ->assertSee('Download label (PNG)', false)
            ->assertSee('AUTOMODZ.PK', false)
            ->assertSee('Drive Your Style', false)
            ->assertSee('COD Amount', false)
            ->assertSee('Parcel Weight', false);
    }

    public function test_admin_can_view_embedded_shipping_label_preview(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $order = Order::create([
            'order_number' => 'AMZ-EMBED-0427',
            'guest_token' => 'embed-guest-token',
            'status' => 'processing',
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'subtotal' => 2600,
            'shipping' => 250,
            'tax' => 0,
            'total' => 2850,
            'currency' => 'PKR',
            'customer_name' => 'Embed Test',
            'customer_email' => 'embed@example.com',
            'customer_phone' => '+92 321 1111111',
            'shipping_address' => 'Test Address',
            'shipping_city' => 'Lahore',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.orders.shipping-label', ['order' => $order, 'embed' => 1]))
            ->assertOk()
            ->assertSee('AMZ-EMBED-0427', false)
            ->assertDontSee('Print shipping label', false);
    }

    public function test_guest_cannot_view_shipping_label(): void
    {
        $order = Order::create([
            'order_number' => 'AP-TEST0001',
            'guest_token' => 'guest-token',
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
            'shipping_address' => 'Test Address',
            'shipping_city' => 'Lahore',
        ]);

        $this->get(route('admin.orders.shipping-label', $order))
            ->assertRedirect();
    }
}
