<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            'Fantasy',
            'Science Fiction',
            'Mystery',
            'Thriller',
            'Romance',
            'Horror',
            'Young Adult',
            'Historical Fiction',
            'Adventure',
            'Nonfiction',
            'Crime',
            'Drama',
            'Comedy',
            'Epic Fantasy',
            'Urban Fantasy',
            'Dystopian',
            'Post-Apocalyptic',
            'Literary Fiction',
        ];

        foreach ($genres as $name) {
            Genre::query()->firstOrCreate(['name' => $name]);
        }
    }
}
