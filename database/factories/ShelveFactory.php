<?php

namespace Database\Factories;

use App\Enums\BookStatus;
use App\Models\BookEdition;
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
            'book_edition_id' => BookEdition::factory(),
            'status' => $this->faker->randomElement(array_column(BookStatus::cases(), 'value')),
            'times_read' => $this->faker->numberBetween(0, 10),
            'favourite' => $this->faker->boolean(25),
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }
}
