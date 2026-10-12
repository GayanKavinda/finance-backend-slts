<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use PHPUnit\Framework\Attributes\Test;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;
    protected $admin;
    protected $finance;
    protected $viewer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $this->superAdmin = User::factory()->create(['name' => 'Super Admin User']);
        $this->superAdmin->assignRole('Super Admin');

        $this->admin = User::factory()->create(['name' => 'Admin User']);
        $this->admin->assignRole('Admin');

        $this->finance = User::factory()->create(['name' => 'Finance User']);
        $this->finance->assignRole('Finance');

        $this->viewer = User::factory()->create(['name' => 'Viewer User']);
        $this->viewer->assignRole('Viewer');
    }

    #[Test]
    public function admin_can_list_users_but_super_admin_is_hidden_from_them()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/users');

        $response->assertStatus(200);

        $data = $response->json('data');
        $emails = collect($data)->pluck('email')->toArray();

        // Admin should see Finance and Viewer
        $this->assertContains($this->finance->email, $emails);
        $this->assertContains($this->viewer->email, $emails);

        // Admin should NOT see Super Admin
        $this->assertNotContains($this->superAdmin->email, $emails);
    }

    #[Test]
    public function super_admin_can_view_all_users_including_other_super_admins()
    {
        $response = $this->actingAs($this->superAdmin)
            ->getJson('/api/admin/users');

        $response->assertStatus(200);

        $data = $response->json('data');
        $emails = collect($data)->pluck('email')->toArray();

        $this->assertContains($this->superAdmin->email, $emails);
        $this->assertContains($this->admin->email, $emails);
    }

    #[Test]
    public function admin_can_assign_roles_to_subordinate_users()
    {
        $targetUser = User::factory()->create();
        $targetUser->assignRole('Viewer');

        $response = $this->actingAs($this->admin)
            ->postJson("/api/admin/users/{$targetUser->id}/assign-role", [
                'role' => 'Finance',
            ]);

        $response->assertStatus(200);
        $this->assertTrue($targetUser->fresh()->hasRole('Finance'));
        $this->assertFalse($targetUser->fresh()->hasRole('Viewer'));
    }

    #[Test]
    public function admin_cannot_elevate_anyone_to_super_admin()
    {
        $targetUser = User::factory()->create();
        $targetUser->assignRole('Viewer');

        $response = $this->actingAs($this->admin)
            ->postJson("/api/admin/users/{$targetUser->id}/assign-role", [
                'role' => 'Super Admin',
            ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_cannot_deactivate_super_admin()
    {
        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/admin/users/{$this->superAdmin->id}/deactivate");

        $response->assertStatus(403);
        $this->assertFalse($this->superAdmin->fresh()->trashed());
    }

    #[Test]
    public function admin_can_deactivate_and_reactivate_standard_users()
    {
        $targetUser = User::factory()->create();
        $targetUser->assignRole('Finance');

        // Deactivate
        $deactResponse = $this->actingAs($this->admin)
            ->deleteJson("/api/admin/users/{$targetUser->id}/deactivate");

        $deactResponse->assertStatus(200);
        $this->assertTrue($targetUser->fresh()->trashed());

        // Reactivate
        $reactResponse = $this->actingAs($this->admin)
            ->postJson("/api/admin/users/{$targetUser->id}/reactivate");

        $reactResponse->assertStatus(200);
        $this->assertFalse($targetUser->fresh()->trashed());
    }

    #[Test]
    public function non_admin_roles_receive_403_on_user_management_endpoints()
    {
        $this->actingAs($this->finance)
            ->getJson('/api/admin/users')
            ->assertStatus(403);

        $this->actingAs($this->viewer)
            ->getJson('/api/admin/users')
            ->assertStatus(403);
    }

    #[Test]
    public function only_super_admin_can_access_system_metrics()
    {
        // Admin is forbidden from system metrics
        $this->actingAs($this->admin)
            ->getJson('/api/system/metrics')
            ->assertStatus(403);

        // Super Admin is permitted
        $this->actingAs($this->superAdmin)
            ->getJson('/api/system/metrics')
            ->assertStatus(200);
    }
}
