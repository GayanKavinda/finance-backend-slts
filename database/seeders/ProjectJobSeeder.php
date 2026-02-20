<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectJobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create jobs for awarded tenders
        \App\Models\Tender::all()->each(function ($tender) {
            // Only create jobs for relevant tender statuses
            if (in_array($tender->status, ['Awarded', 'In Progress', 'Completed'])) {
                \App\Models\ProjectJob::factory()->create([
                    'tender_id' => $tender->id,
                    'customer_id' => $tender->customer_id,
                ]);
            }
        });
    }
}
