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
            'reject-invoice',
            'view-audit-trail',
            'manage-users',

            // Roadmap - Procurement
            'manage-customers',
            'manage-tenders',
            'manage-jobs',
            'manage-pos',

            // Roadmap - Finance
            'record-payment',
            'mark-banked',

            // Roadmap - Admin
            'manage-roles',

            // Contractor Management
            'manage-contractors',
            'enter-quotations',
            'select-contractor',
            'verify-contractor-bill',
            'submit-contractor-bill',
            'approve-contractor-payment',
            'mark-contractor-paid',

            // Super Admin only
            'manage-system',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        //Roles
        $superAdmin  = Role::firstOrCreate(['name' => 'Super Admin',  'guard_name' => 'web']);
        $admin       = Role::firstOrCreate(['name' => 'Admin',        'guard_name' => 'web']);
        $procurement = Role::firstOrCreate(['name' => 'Procurement',  'guard_name' => 'web']);
        $finance     = Role::firstOrCreate(['name' => 'Finance',      'guard_name' => 'web']);
        $viewer      = Role::firstOrCreate(['name' => 'Viewer',       'guard_name' => 'web']);

        // Super Admin gets ALL permissions (including manage-system)
        $superAdmin->syncPermissions(Permission::all());

        // Admin gets all permissions EXCEPT manage-system (no system infra access)
        $admin->syncPermissions(
            Permission::where('name', '!=', 'manage-system')->get()
        );

        $procurement->syncPermissions([
            'create-invoice',
            'edit-invoice',
            'submit-invoice',
            'view-invoice',
            'manage-customers',
            'manage-tenders',
            'manage-jobs',
            'manage-pos',
            'manage-contractors',
            'enter-quotations',
            'select-contractor',
            'verify-contractor-bill',
            'submit-contractor-bill',
        ]);

        $finance->syncPermissions([
            'view-invoice',
            'approve-payment',
            'reject-invoice',
            'view-audit-trail',
            'record-payment',
            'mark-banked',
            'approve-contractor-payment',
            'mark-contractor-paid',
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
                ['Super Admin', implode(', ', $superAdmin->permissions->pluck('name')->toArray())],
                ['Admin',       implode(', ', $admin->permissions->pluck('name')->toArray())],
                ['Procurement', implode(', ', $procurement->permissions->pluck('name')->toArray())],
                ['Finance',     implode(', ', $finance->permissions->pluck('name')->toArray())],
                ['Viewer',      implode(', ', $viewer->permissions->pluck('name')->toArray())],
            ]
        );
    }
}
