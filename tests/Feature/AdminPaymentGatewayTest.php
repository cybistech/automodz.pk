<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\PaymentGateway;
use App\Models\Product;
use App\Models\ShippingCity;
use App\Models\User;
use App\Services\PaymentGatewayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PaymentGatewayService::class)->ensureSeeded();
    }

    public function test_admin_can_view_payment_gateway_settings(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('admin.payment-gateways.edit'))
            ->assertOk()
            ->assertSee('Stripe (Cards)', false)
            ->assertSee('PayPal', false);
    }

    public function test_admin_can_enable_easypaisa_gateway(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->patch(route('admin.payment-gateways.update'), [
                'gateways' => [
                    'easypaisa' => [
                        'is_enabled' => '1',
                        'settings' => [
                            'account_title' => 'AutoModz',
                            'account_number' => '03001234567',
                        ],
                    ],
                ],
            ])
            ->assertRedirect(route('admin.payment-gateways.edit'))
            ->assertSessionHas('success');

        $easypaisa = PaymentGateway::where('key', 'easypaisa')->first();

        $this->assertTrue($easypaisa->is_enabled);
        $this->assertSame('03001234567', $easypaisa->setting('account_number'));
    }

    public function test_checkout_shows_enabled_payment_methods(): void
    {
        PaymentGateway::where('key', 'cod')->update(['is_enabled' => true]);

        $product = $this->createProduct();
        ShippingCity::create([
            'name' => 'Karachi',
            'distance_km' => 0,
            'base_fee' => 250,
            'rate_per_km' => 0,
            'is_active' => true,
        ]);

        $this->post(route('cart.add', $product), ['quantity' => 1]);

        $this->get(route('checkout.index'))
            ->assertOk()
            ->assertSee('Cash on Delivery', false);
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
}
