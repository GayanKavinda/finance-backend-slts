<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 1 PO for each existing job
        \App\Models\ProjectJob::all()->each(function ($job) {
            \App\Models\PurchaseOrder::factory()->create([
                'job_id' => $job->id,
                'tender_id' => $job->tender_id,
                'customer_id' => $job->customer_id,
            ]);
        });
    }
}
