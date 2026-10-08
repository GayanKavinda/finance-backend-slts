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
        // Create 1 invoice for each purchase order
        \App\Models\PurchaseOrder::all()->each(function ($po) {
            \App\Models\Invoice::factory()->create([
                'po_id' => $po->id,
                'customer_id' => $po->customer_id,
            ]);
        });
    }
}
