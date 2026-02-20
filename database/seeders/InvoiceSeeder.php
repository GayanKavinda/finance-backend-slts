<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create invoices for approved POs
        \App\Models\PurchaseOrder::all()->each(function ($po) {
            if ($po->status === 'Approved') {
                \App\Models\Invoice::factory(rand(1, 2))->create([
                    'po_id' => $po->id,
                    'customer_id' => $po->customer_id,
                ]);
            }
        });
    }
}
