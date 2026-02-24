<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'po_number' => $this->faker->unique()->bothify('PO-####-??'),
            'po_date' => $this->faker->date(),
            'job_id' => \App\Models\ProjectJob::factory(),
            'po_description' => $this->faker->sentence(),
            'po_amount' => $this->faker->randomFloat(2, 1000, 50000),
            'billing_address' => $this->faker->address(),
            'tender_id' => \App\Models\Tender::factory(),
            'customer_id' => \App\Models\Customer::factory(),
            'status' => $this->faker->randomElement(['Draft', 'Approved']),
        ];
    }
}
