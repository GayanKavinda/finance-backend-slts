<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contractor;

class ContractorSeeder extends Seeder
{
    public function run(): void
    {
        Contractor::factory(10)->create();
    }
}
