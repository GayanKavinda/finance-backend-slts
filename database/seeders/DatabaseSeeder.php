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
            TenderSeeder::class,
            ProjectJobSeeder::class,
            PurchaseOrderSeeder::class,
            InvoiceSeeder::class,
            ChequeTransactionSeeder::class,
        ]);

        // Create specific users for each role
        $roleUsers = [
            [
                'name' => 'Admin User',
                'email' => 'admin@finance.com',
                'password' => 'password',
                'role' => 'Admin',
            ],
            [
                'name' => 'Procurement User',
                'email' => 'procurement@finance.com',
                'password' => 'password',
                'role' => 'Procurement',
            ],
            [
                'name' => 'Finance User',
                'email' => 'finance@finance.com',
                'password' => 'password',
                'role' => 'Finance',
            ],
            [
                'name' => 'Viewer User',
                'email' => 'viewer@finance.com',
                'password' => 'password',
                'role' => 'Viewer',
            ],
        ];

        foreach ($roleUsers as $userData) {
            $user = User::factory()->create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => bcrypt($userData['password']),
            ]);
            $user->assignRole($userData['role']);
        }

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Ensure Test User has Admin role
        $user = User::where('email', 'test@example.com')->first();
        if ($user) {
            $user->assignRole('Admin');
        }
    }
}
