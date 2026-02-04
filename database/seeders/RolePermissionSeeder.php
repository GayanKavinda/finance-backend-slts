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
            'create-invoice',
            'edit-invoice',
            'submit-invoice',
            'view-invoice',
            'approve-payment',
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
        $admin->givePermissionTo(Permission::all());

        $procurement->givePermissionTo([
            'create-invoice',
            'edit-invoice',
            'submit-invoice',
            'view-invoice',
        ]);

        $finance->givePermissionTo([
            'view-invoice',
            'approve-payment'
        ]);

        $viewer->givePermissionTo([
            'view-invoice'
        ]);
    }
}
