<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            CustomerSeeder::class,
            ContractorSeeder::class,
            TenderSeeder::class,
            ProjectJobSeeder::class,
            PurchaseOrderSeeder::class,
            ContractorBillSeeder::class,
            InvoiceSeeder::class,
            ChequeTransactionSeeder::class,
        ]);

        // Create specific users for each role
        $roleUsers = [
            [
                'name'     => 'Super Admin',
                'email'    => 'superadmin@finance.com',
                'password' => 'password',
                'role'     => 'Super Admin',
            ],
            [
                'name'     => 'Admin User',
                'email'    => 'admin@finance.com',
                'password' => 'password',
                'role'     => 'Admin',
            ],
            [
                'name'     => 'Procurement User',
                'email'    => 'procurement@finance.com',
                'password' => 'password',
                'role'     => 'Procurement',
            ],
            [
                'name'     => 'Finance User',
                'email'    => 'finance@finance.com',
                'password' => 'password',
                'role'     => 'Finance',
            ],
            [
                'name'     => 'Viewer User',
                'email'    => 'viewer@finance.com',
                'password' => 'password',
                'role'     => 'Viewer',
            ],
        ];

        foreach ($roleUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name'     => $userData['name'],
                    'password' => bcrypt($userData['password']),
                ]
            );
            $user->syncRoles([$userData['role']]);
        }
    }
}
