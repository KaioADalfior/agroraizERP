<?php

namespace Database\Factories;

use App\Models\AnaliseSolo;
use App\Models\Cultura;
use App\Models\Propriedade;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AnaliseSolo>
 */
class AnaliseSoloFactory extends Factory
{
    protected $model = AnaliseSolo::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'propriedade_id' => Propriedade::factory(),
            'cultura_id' => Cultura::inRandomOrder()->value('id'),
            'profundidade' => '00-20 cm',
            'data_coleta' => fake()->optional()->date(),
            'ph' => fake()->randomFloat(2, 4, 7),
            'ctc' => fake()->randomFloat(2, 3, 15),
            'v_percentual' => fake()->randomFloat(2, 20, 80),
            'm_percentual' => fake()->randomFloat(2, 0, 30),
        ];
    }
}
