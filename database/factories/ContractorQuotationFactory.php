<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ContractorQuotationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'job_id' => \App\Models\ProjectJob::factory(),
            'contractor_id' => \App\Models\Contractor::factory(),
            'quotation_amount' => $this->faker->randomFloat(2, 4000, 450000),
            'quotation_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'work_scope' => $this->faker->paragraph(),
            'estimated_days' => $this->faker->numberBetween(5, 60),
            'status' => $this->faker->randomElement(['Pending', 'Selected', 'Rejected']),
        ];
    }
}
