<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
            'cep' => fake()->numerify('#####-###'),
            'endereco' => fake()->streetName(),
            'numero' => fake()->optional()->buildingNumber(),
            'complemento' => fake()->optional()->secondaryAddress(),
            'bairro' => Str::title(fake()->words(2, true)),
            'cidade' => fake()->city(),
            'estado' => fake()->randomElement(array_keys(Cliente::ESTADOS)),
            'data' => fake()->optional()->date(),
            'observacao' => fake()->optional()->sentence(),
        ];
    }
}
