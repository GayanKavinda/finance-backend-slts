<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'po_id' => \App\Models\PurchaseOrder::factory(),
            'customer_id' => \App\Models\Customer::factory(), // Should match PO's customer in seeder
            'invoice_number' => $this->faker->unique()->bothify('INV-####-??'),
            'invoice_amount' => $this->faker->randomFloat(2, 500, 20000),
            'invoice_date' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'status' => $this->faker->randomElement(['Draft', 'Tax Generated', 'Submitted', 'Approved', 'Paid']),

            // Payment fields (optional)
            'payment_reference' => $this->faker->optional()->bothify('PAY-####'),
            'payment_method' => $this->faker->optional()->randomElement(['Bank Transfer', 'Cheque', 'Cash']),
            'paid_at' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
