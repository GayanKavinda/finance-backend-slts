<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ContractorBillFactory extends Factory
{
    public function definition(): array
    {
        return [
            'job_id' => \App\Models\ProjectJob::factory(),
            'contractor_id' => \App\Models\Contractor::factory(),
            'bill_number' => $this->faker->unique()->bothify('BILL-####-??'),
            'bill_amount' => $this->faker->randomFloat(2, 500, 50000),
            'bill_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'status' => 'Draft',
        ];
    }
}
