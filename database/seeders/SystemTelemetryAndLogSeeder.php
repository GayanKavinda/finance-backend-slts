<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SystemTelemetryAndLogSeeder extends Seeder
{
    public function run(): void
    {
        $superadmin = User::where('email', 'superadmin@finance.com')->first();
        $admin = User::where('email', 'admin@finance.com')->first();
        $procurement = User::where('email', 'procurement@finance.com')->first();
        $finance = User::where('email', 'finance@finance.com')->first();

        // 1. Seed Login Activities
        $users = [$superadmin, $admin, $procurement, $finance];
        foreach ($users as $idx => $user) {
            if (!$user) continue;

            DB::table('login_activities')->insert([
                [
                    'user_id'    => $user->id,
                    'ip_address' => '192.168.1.' . (10 + $idx),
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/122.0.0.0 Safari/537.36',
                    'device'     => 'Desktop',
                    'platform'   => 'Windows',
                    'browser'    => 'Chrome',
                    'status'     => 'success',
                    'created_at' => now()->subHours(12 - $idx),
                ],
                [
                    'user_id'    => $user->id,
                    'ip_address' => '192.168.1.' . (10 + $idx),
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/122.0.0.0 Safari/537.36',
                    'device'     => 'Desktop',
                    'platform'   => 'Windows',
                    'browser'    => 'Chrome',
                    'status'     => 'success',
                    'created_at' => now()->subMinutes(30),
                ]
            ]);
        }

        // 2. Seed Audit Logs
        if ($superadmin) {
            DB::table('audit_logs')->insert([
                [
                    'user_id'     => $superadmin->id,
                    'action'      => 'CREATE_TENDER',
                    'entity_type' => 'App\\Models\\Tender',
                    'entity_id'   => 1,
                    'timestamp'   => now()->subDays(5),
                ],
                [
                    'user_id'     => $procurement?->id ?? $superadmin->id,
                    'action'      => 'APPROVE_CONTRACTOR_BILL',
                    'entity_type' => 'App\\Models\\ContractorBill',
                    'entity_id'   => 1,
                    'timestamp'   => now()->subDays(3),
                ],
                [
                    'user_id'     => $finance?->id ?? $superadmin->id,
                    'action'      => 'RECORD_INVOICE_PAYMENT',
                    'entity_type' => 'App\\Models\\Invoice',
                    'entity_id'   => 1,
                    'timestamp'   => now()->subDays(1),
                ],
                [
                    'user_id'     => $superadmin->id,
                    'action'      => 'UPDATE_SYSTEM_SETTINGS',
                    'entity_type' => 'System',
                    'entity_id'   => 0,
                    'timestamp'   => now()->subHours(4),
                ],
            ]);
        }

        // 3. Seed Notifications
        if ($superadmin) {
            DB::table('notifications')->insert([
                [
                    'id'              => (string) Str::uuid(),
                    'type'            => 'App\\Notifications\\TenderAwardedNotification',
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id'   => $superadmin->id,
                    'data'            => json_encode([
                        'title'   => 'Tender Awarded',
                        'message' => 'Fibre Optic Network Expansion has been officially marked as Awarded.',
                        'url'     => '/tenders/1',
                    ]),
                    'read_at'         => null,
                    'created_at'      => now()->subHours(2),
                    'updated_at'      => now()->subHours(2),
                ],
                [
                    'id'              => (string) Str::uuid(),
                    'type'            => 'App\\Notifications\\BillSubmittedNotification',
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id'   => $superadmin->id,
                    'data'            => json_encode([
                        'title'   => 'Contractor Bill Submitted',
                        'message' => 'Contractor Bill CB-2026-0003 is awaiting review and approval.',
                        'url'     => '/contractor-bills',
                    ]),
                    'read_at'         => now()->subHours(1),
                    'created_at'      => now()->subHours(1),
                    'updated_at'      => now()->subHours(1),
                ]
            ]);
        }

        $this->command->info('✅ SystemTelemetryAndLogSeeder: Login activities, audit logs, and notifications seeded.');
    }
}
