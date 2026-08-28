<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceModeTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_can_access_system_when_maintenance_is_off(): void
    {
        SystemSetting::set('maintenance_mode', false);

        $admin = User::factory()->create(['role' => 'Admin']);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_warehouse_manager_can_access_system_and_toggle_maintenance(): void
    {
        $manager = User::factory()->create(['role' => 'Warehouse Manager']);

        // Enable maintenance
        $response = $this->actingAs($manager)->post('/maintenance-settings', [
            'is_active' => true,
            'message' => 'Maintenance testing in progress.',
            'estimated_finish' => '1 hour',
        ]);
        $response->assertSessionHasNoErrors();
        $this->assertTrue(SystemSetting::isMaintenanceMode());

        // Manager can still access pages
        $dashboardResponse = $this->actingAs($manager)->get('/dashboard');
        $dashboardResponse->assertStatus(200);

        $settingsResponse = $this->actingAs($manager)->get('/maintenance-settings');
        $settingsResponse->assertStatus(200);
    }

    public function test_non_manager_gets_503_when_maintenance_is_on(): void
    {
        SystemSetting::set('maintenance_mode', true);
        SystemSetting::set('maintenance_message', 'System is down for scheduled maintenance.');

        $admin = User::factory()->create(['role' => 'Admin']);

        // Regular user should get 503
        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(503);

        // Login page should still be accessible
        $loginResponse = $this->get('/login');
        $loginResponse->assertStatus(200);
    }

    public function test_non_manager_cannot_change_maintenance_settings(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        $response = $this->actingAs($admin)->post('/maintenance-settings', [
            'is_active' => false,
        ]);
        
        $response->assertStatus(403);
    }
}
