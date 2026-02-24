<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ContractorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'contact_person' => $this->faker->name(),
            'email' => $this->faker->unique()->companyEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'tax_id' => $this->faker->bothify('TIN-#######'),
            'bank_name' => $this->faker->randomElement(['Bank of Ceylon', 'Commercial Bank', 'Sampath Bank']),
            'bank_account_number' => $this->faker->bankAccountNumber(),
            'status' => 'Active',
            'rating' => $this->faker->numberBetween(1, 5),
        ];
    }
}
