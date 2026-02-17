<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        //Permissions
        $permissions = [
            // Invoice permissions
            'create-invoice',
            'edit-invoice',
            'submit-invoice',
            'view-invoice',
            'approve-payment',
            'reject-invoice',      // NEW: Finance can reject
            'view-audit-trail',    // NEW: View status history
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        //Roles
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $procurement = Role::firstOrCreate(['name' => 'Procurement']);
        $finance = Role::firstOrCreate(['name' => 'Finance']);
        $viewer = Role::firstOrCreate(['name' => 'Viewer']);

        // Assign permissions
        $admin->syncPermissions(Permission::all());

        $procurement->syncPermissions([
            'create-invoice',
            'edit-invoice',
            'submit-invoice',
            'view-invoice',
        ]);

        $finance->syncPermissions([
            'view-invoice',
            'approve-payment',
            'reject-invoice',
            'view-audit-trail',
        ]);

        $viewer->syncPermissions([
            'view-invoice'
        ]);

        $this->command->info('✅ Permissions and roles seeded successfully!');
        $this->command->newLine();
        $this->command->info('📋 Roles & Permissions Summary:');
        $this->command->newLine();

        $this->command->table(
            ['Role', 'Permissions'],
            [
                ['Admin', implode(', ', $admin->permissions->pluck('name')->toArray())],
                ['Procurement', implode(', ', $procurement->permissions->pluck('name')->toArray())],
                ['Finance', implode(', ', $finance->permissions->pluck('name')->toArray())],
                ['Viewer', implode(', ', $viewer->permissions->pluck('name')->toArray())],
            ]
        );
    }
}
