<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_layout_includes_logout(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Log out', false)
            ->assertSee(route('logout'), false);
    }

    public function test_admin_can_logout(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->post(route('logout'))
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
