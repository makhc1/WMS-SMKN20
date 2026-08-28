<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_all_sidebar_routes(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        $response = $this->actingAs($admin)->get('/riwayat');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/locations');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/reports');
        $response->assertStatus(200);
    }
}
