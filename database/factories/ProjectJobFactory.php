<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectJob>
 */
class ProjectJobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tender_id' => \App\Models\Tender::factory(),
            'customer_id' => \App\Models\Customer::factory(),
            'name' => $this->faker->catchPhrase(),
            'project_value' => $this->faker->randomFloat(2, 5000, 500000),
            'description' => $this->faker->paragraph(),
            'contractor_id' => null, // Can be set via seeder or state
            'status' => $this->faker->randomElement(['Pending', 'In Progress', 'Completed', 'On Hold']),
            'completed_at' => $this->faker->optional(0.3)->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
