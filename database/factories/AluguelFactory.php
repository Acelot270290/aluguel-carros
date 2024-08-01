<?php

namespace Database\Factories;

use App\Models\Aluguel;
use App\Models\User;
use App\Models\Carro;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Aluguel>
 */
class AluguelFactory extends Factory
{
    protected $model = Aluguel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'carro_id' => Carro::factory(),
            'data_inicio' => $this->faker->date(),
            'data_fim' => $this->faker->date(),
            'valor' => $this->faker->randomFloat(2, 100, 1000),
        ];
    }
}
