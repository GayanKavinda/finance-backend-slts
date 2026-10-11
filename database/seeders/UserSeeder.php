<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'               => 'Kavindu Perera',
                'email'              => 'superadmin@finance.com',
                'password'           => 'password',
                'role'               => 'Super Admin',
                'phone'              => '+94 77 123 4567',
                'address'            => 'No. 42, Bauddhaloka Mawatha',
                'city'               => 'Colombo',
                'country'            => 'Sri Lanka',
                'postal_code'        => '00300',
                'date_of_birth'      => '1985-03-15',
                'gender'             => 'Male',
                'company'            => 'SLT Mobitel (Pvt) Ltd',
                'job_title'          => 'System Administrator',
                'tax_id'             => 'TIN-SLT-00192',
                'profile_updated_by' => 'system',
            ],
            [
                'name'               => 'Amara Silva',
                'email'              => 'admin@finance.com',
                'password'           => 'password',
                'role'               => 'Admin',
                'phone'              => '+94 71 234 5678',
                'address'            => 'No. 18, Galle Road',
                'city'               => 'Colombo',
                'country'            => 'Sri Lanka',
                'postal_code'        => '00600',
                'date_of_birth'      => '1988-07-22',
                'gender'             => 'Female',
                'company'            => 'SLT Mobitel (Pvt) Ltd',
                'job_title'          => 'Finance Administrator',
                'tax_id'             => 'TIN-SLT-00234',
                'profile_updated_by' => 'system',
            ],
            [
                'name'               => 'Ruwan Dissanayake',
                'email'              => 'procurement@finance.com',
                'password'           => 'password',
                'role'               => 'Procurement',
                'phone'              => '+94 76 345 6789',
                'address'            => 'No. 55, Kandy Road, Kelaniya',
                'city'               => 'Kelaniya',
                'country'            => 'Sri Lanka',
                'postal_code'        => '11600',
                'date_of_birth'      => '1990-11-08',
                'gender'             => 'Male',
                'company'            => 'SLT Mobitel (Pvt) Ltd',
                'job_title'          => 'Procurement Officer',
                'tax_id'             => 'TIN-SLT-00356',
                'profile_updated_by' => 'system',
            ],
            [
                'name'               => 'Nirmala Fernando',
                'email'              => 'finance@finance.com',
                'password'           => 'password',
                'role'               => 'Finance',
                'phone'              => '+94 72 456 7890',
                'address'            => 'No. 8, Ward Place, Colombo 7',
                'city'               => 'Colombo',
                'country'            => 'Sri Lanka',
                'postal_code'        => '00700',
                'date_of_birth'      => '1992-04-30',
                'gender'             => 'Female',
                'company'            => 'SLT Mobitel (Pvt) Ltd',
                'job_title'          => 'Finance Manager',
                'tax_id'             => 'TIN-SLT-00478',
                'profile_updated_by' => 'system',
            ],
            [
                'name'               => 'Thilina Rajapaksha',
                'email'              => 'viewer@finance.com',
                'password'           => 'password',
                'role'               => 'Viewer',
                'phone'              => '+94 70 567 8901',
                'address'            => 'No. 22, Dutugemunu Street, Dehiwala',
                'city'               => 'Dehiwala',
                'country'            => 'Sri Lanka',
                'postal_code'        => '10350',
                'date_of_birth'      => '1995-09-14',
                'gender'             => 'Male',
                'company'            => 'SLT Mobitel (Pvt) Ltd',
                'job_title'          => 'Audit Viewer',
                'tax_id'             => 'TIN-SLT-00512',
                'profile_updated_by' => 'system',
            ],
        ];

        foreach ($users as $data) {
            $role = $data['role'];
            unset($data['role'], $data['password']);

            $user = User::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'password'           => Hash::make('password'),
                    'email_verified_at'  => now(),
                    'login_attempts'     => 0,
                    'profile_updated_at' => now(),
                ])
            );

            $user->syncRoles([$role]);
        }

        $this->command->info('✅ UserSeeder: 5 users seeded with full profile data.');
    }
}
