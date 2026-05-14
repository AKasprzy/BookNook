<?php

namespace Database\Factories;

use App\Models\BookEdition;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'book_edition_id' => BookEdition::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'review_text' => $this->faker->paragraph(3),
            'spoiler' => $this->faker->boolean(),
            'reread' => $this->faker->boolean(),
            'reviewed_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
