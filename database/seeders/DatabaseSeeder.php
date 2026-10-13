<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            UserSeeder::class,
            CustomerSeeder::class,
            ContractorSeeder::class,
            TenderSeeder::class,
            ProjectJobSeeder::class,
            ContractorQuotationSeeder::class,
            PurchaseOrderSeeder::class,
            ContractorBillSeeder::class,
            InvoiceSeeder::class,
            ChequeTransactionSeeder::class,
            SystemTelemetryAndLogSeeder::class,
            SupportTicketSeeder::class,
        ]);
    }
}
