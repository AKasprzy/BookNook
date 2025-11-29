<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Shelve;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShelveFactory extends Factory
{
    protected $model = Shelve::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'status' => $this->faker->optional()->randomElement(['read', 'reading', 'tbr', 'dnf']),
            'times_read' => $this->faker->numberBetween(0, 10),
            'favourite' => $this->faker->boolean(25),
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }
}
