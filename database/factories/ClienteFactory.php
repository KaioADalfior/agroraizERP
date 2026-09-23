<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cliente>
 */
class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'cidade_estado' => fake()->city().'-'.fake()->stateAbbr(),
            'data' => fake()->optional()->date(),
            'observacao' => fake()->optional()->sentence(),
        ];
    }
}
