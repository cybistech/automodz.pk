<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ShippingCity;
use App\Models\User;
use App\Services\CartService;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class GuestCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_access_checkout(): void
    {
        $response = $this->get(route('checkout.index'));

        $response->assertStatus(302);
    }

    public function test_guest_can_place_cod_order(): void
    {
        $product = $this->createProduct();
        $city = $this->createShippingCity();

        $this->post(route('cart.add', $product), ['quantity' => 1]);

        $response = $this->post(route('checkout.store'), [
            'customer_name' => 'Guest User',
            'customer_email' => 'guest@example.com',
            'customer_phone' => '03001234567',
            'shipping_address' => '123 Test Street',
            'shipping_city_id' => $city->id,
            'payment_method' => 'cod',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'customer_email' => 'guest@example.com',
            'user_id' => null,
            'shipping_city' => 'Karachi',
            'tax' => 0,
            'shipping' => 250,
        ]);
    }

    public function test_guest_can_track_order(): void
    {
        $product = $this->createProduct();
        $city = $this->createShippingCity();

        $this->post(route('cart.add', $product), ['quantity' => 1]);

        $this->post(route('checkout.store'), [
            'customer_name' => 'Guest User',
            'customer_email' => 'guest@example.com',
            'customer_phone' => '03001234567',
            'shipping_address' => '123 Test Street',
            'shipping_city_id' => $city->id,
            'payment_method' => 'cod',
        ]);

        $order = \App\Models\Order::first();

        $response = $this->post(route('orders.track.submit'), [
            'order_number' => $order->order_number,
            'contact' => 'guest@example.com',
        ]);

        $response->assertRedirect(route('orders.confirmation', [
            'order' => $order,
            'token' => $order->guest_token,
        ]));
    }

    public function test_mobile_login_with_otp(): void
    {
        $otp = app(OtpService::class);
        $phone = '+923001112233';
        $otp->send($phone);
        $code = Cache::get('otp:'.md5($phone));

        $response = $this->post(route('login.mobile.verify'), [
            'phone' => $phone,
            'code' => $code,
            'name' => 'Mobile User',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['phone' => $phone]);
    }

    public function test_login_links_previous_guest_orders(): void
    {
        $product = $this->createProduct();
        $lahore = ShippingCity::create([
            'name' => 'Lahore',
            'distance_km' => 1200,
            'base_fee' => 350,
            'rate_per_km' => 0,
            'is_active' => true,
        ]);

        $this->post(route('cart.add', $product), ['quantity' => 1]);

        $this->post(route('checkout.store'), [
            'customer_name' => 'Guest User',
            'customer_email' => 'linkme@example.com',
            'customer_phone' => '03009998877',
            'shipping_address' => '123 Test Street',
            'shipping_city_id' => $lahore->id,
            'payment_method' => 'cod',
        ]);

        $user = User::factory()->create([
            'email' => 'linkme@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->post(route('login'), [
            'email' => 'linkme@example.com',
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('orders', [
            'customer_email' => 'linkme@example.com',
            'user_id' => $user->id,
            'shipping_city' => 'Lahore',
        ]);
    }

    private function createProduct(): Product
    {
        $category = Category::create([
            'name' => 'Test Cat',
            'slug' => 'test-cat',
            'is_active' => true,
        ]);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'sku' => 'TEST-001',
            'price' => 1000,
            'stock' => 10,
            'condition' => 'new',
            'is_active' => true,
        ]);
    }

    private function createShippingCity(): ShippingCity
    {
        return ShippingCity::create([
            'name' => 'Karachi',
            'distance_km' => 0,
            'base_fee' => 250,
            'rate_per_km' => 0,
            'is_active' => true,
        ]);
    }
}
