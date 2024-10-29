<?php

namespace Database\Factories;

use App\Models\Contract;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContractFactory extends Factory
{
    protected $model = Contract::class;

    public function definition()
    {
        $faker = \Faker\Factory::create('pt_BR');

        $contractorType = $this->faker->randomElement(['individual', 'company']);
        $contractorCnpjCpf = $contractorType === 'individual' ? $faker->unique()->cpf(false) : $faker->unique()->cnpj(false);

        return [
            'cpf_cnpj' => $contractorCnpjCpf,
            'contractor_type' => $contractorType,
            'company_count' => $this->faker->numberBetween(1, 10),
            'user_count' => $this->faker->numberBetween(1, 50),
            'status' => $this->faker->boolean(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
