<?php

namespace Tests\Feature;

use App\Models\SsoProvider;
use App\Models\User;
use App\Services\SsoProviderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSsoProviderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(SsoProviderService::class)->ensureSeeded();
    }

    public function test_admin_can_view_sso_settings(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('admin.sso-providers.edit'))
            ->assertOk()
            ->assertSee('Gmail (Google)', false)
            ->assertSee('Instagram', false);
    }

    public function test_admin_can_enable_google_sso(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->patch(route('admin.sso-providers.update'), [
                'providers' => [
                    'google' => [
                        'is_enabled' => '1',
                        'client_id' => 'google-client-id',
                        'client_secret' => 'google-client-secret',
                    ],
                ],
            ])
            ->assertRedirect(route('admin.sso-providers.edit'))
            ->assertSessionHas('success');

        $google = SsoProvider::where('key', 'google')->first();

        $this->assertTrue($google->is_enabled);
        $this->assertSame('google-client-id', $google->client_id);
        $this->assertSame('google-client-secret', $google->client_secret);
    }

    public function test_enabled_sso_buttons_appear_on_login_page(): void
    {
        SsoProvider::where('key', 'google')->update([
            'is_enabled' => true,
            'client_id' => 'id',
            'client_secret' => 'secret',
        ]);

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Continue with Google', false)
            ->assertSee(route('social.redirect', ['provider' => 'google']), false);
    }

    public function test_disabled_sso_buttons_hidden_on_login_page(): void
    {
        SsoProvider::query()->update(['is_enabled' => false]);

        $this->get(route('login'))
            ->assertOk()
            ->assertDontSee('Continue with Google', false);
    }

    public function test_guest_cannot_access_sso_admin_settings(): void
    {
        $this->get(route('admin.sso-providers.edit'))
            ->assertRedirect(route('login'));
    }
}
