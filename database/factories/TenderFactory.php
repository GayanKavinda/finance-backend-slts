<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tender>
 */
class TenderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tender_number' => $this->faker->unique()->bothify('TND-####-??'),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'customer_id' => \App\Models\Customer::factory(),
            'awarded_amount' => $this->faker->randomFloat(2, 10000, 1000000),
            'budget' => $this->faker->randomFloat(2, 5000, 500000),
            'start_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'end_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'status' => $this->faker->randomElement(['Open', 'Awarded', 'In Progress', 'Completed']),
        ];
    }
}
