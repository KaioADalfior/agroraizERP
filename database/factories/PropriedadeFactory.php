<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Propriedade;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Propriedade>
 */
class PropriedadeFactory extends Factory
{
    protected $model = Propriedade::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'nome' => 'Fazenda '.fake()->lastName(),
            'area_hectares' => fake()->optional()->randomFloat(2, 1, 500),
            'localizacao' => fake()->optional()->city(),
            'observacao' => fake()->optional()->sentence(),
        ];
    }
}
