<?php

namespace Database\Factories;

use App\Enums\BookFormat;
use App\Models\Book;
use App\Models\BookEdition;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookEditionFactory extends Factory
{
    protected $model = BookEdition::class;

    public function definition(): array
    {
        $bookId = Book::inRandomOrder()->value('id') ?? Book::factory()->create()->id;

        return [
            'book_id' => $bookId,
            'edition_title' => $this->faker->sentence(3),
            'edition_publication_date' => $this->faker->optional()->date(),
            'format' => $this->faker->randomElement(BookFormat::cases())->value,
            'edition_language' => $this->faker->languageCode(),
            'description' => $this->faker->optional()->paragraph(),
            'isbn' => $this->faker->optional()->isbn13(),
            'page_count' => $this->faker->optional()->numberBetween(10, 1200),
            'length_minutes' => $this->faker->optional()->numberBetween(60, 3600),
            'cover_url' => $this->faker->optional()->imageUrl(),
            'publisher' => $this->faker->optional()->company(),
        ];
    }
}
