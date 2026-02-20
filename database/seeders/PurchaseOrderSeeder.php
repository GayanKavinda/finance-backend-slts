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
        // Create POs for existing jobs
        \App\Models\ProjectJob::all()->each(function ($job) {
            \App\Models\PurchaseOrder::factory(rand(1, 2))->create([
                'job_id' => $job->id,
                'tender_id' => $job->tender_id,
                'customer_id' => $job->customer_id,
            ]);
        });
    }
}
