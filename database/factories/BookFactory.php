<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\BookEdition;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'original_language' => $this->faker->languageCode(),
            'author' => $this->faker->name(),
            'original_publication_date' => $this->faker->optional()->date(),
            'series' => $this->faker->optional()->word(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Book $book) {
            BookEdition::factory()->for($book)->create();
        });
    }
}
