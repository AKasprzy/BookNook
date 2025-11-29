<?php

namespace Database\Factories;

use App\Models\Motif;
use Illuminate\Database\Eloquent\Factories\Factory;

class MotifFactory extends Factory
{
    protected $model = Motif::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
        ];
    }
}
