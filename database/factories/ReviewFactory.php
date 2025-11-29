<?php

namespace Database\Factories;

use App\Models\Book;
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
            'book_id' => Book::factory(),
            'rating' => $this->faker->optional()->numberBetween(1, 10),
            'review_text' => $this->faker->optional()->paragraph(3),
            'spoiler' => $this->faker->boolean(20),
            'reread' => $this->faker->boolean(15),
            'reviewed_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
