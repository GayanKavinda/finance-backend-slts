<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 1 tender for each existing customer (5 tenders total)
        \App\Models\Customer::all()->each(function ($customer) {
            \App\Models\Tender::factory()->create([
                'customer_id' => $customer->id,
            ]);
        });
    }
}
