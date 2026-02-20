<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChequeTransaction>
 */
class ChequeTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_id' => \App\Models\Invoice::factory(),
            'payer_name' => $this->faker->name(),
            'payer_account_number' => $this->faker->numerify('##########'),
            'payer_bank_name' => $this->faker->company() . ' Bank',
            'payer_bank_branch' => $this->faker->city(),
            'payer_bank_code' => $this->faker->numerify('##-##-##'),
            'cheque_number' => $this->faker->numerify('######'),
            'cheque_date' => $this->faker->date(),
            'amount' => $this->faker->randomFloat(2, 500, 20000),
            'amount_in_words' => 'Five hundred dollars only', // Placeholder string
            'payee_name' => 'Finance App Corp',
            'status' => $this->faker->randomElement(['Pending', 'Cleared', 'Bounced']),
        ];
    }
}
