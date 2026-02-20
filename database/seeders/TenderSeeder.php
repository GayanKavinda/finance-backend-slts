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
        // Create tenders for existing customers
        \App\Models\Customer::all()->each(function ($customer) {
            \App\Models\Tender::factory(rand(1, 3))->create([
                'customer_id' => $customer->id
            ]);
        });
    }
}
