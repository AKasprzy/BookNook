<?php

namespace Database\Seeders;

use App\Models\BookEdition;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        Review::truncate();

        $users = User::all();
        $editions = BookEdition::all();

        if ($users->isEmpty() || $editions->isEmpty()) {
            $this->command->info('No users or book editions available to seed reviews.');

            return;
        }

        foreach ($users as $user) {
            $editions->random(rand(1, 5))->each(function (BookEdition $edition) use ($user) {
                Review::create([
                    'user_id' => $user->id,
                    'book_edition_id' => $edition->id,
                    'rating' => rand(1, 5),
                    'review_text' => 'This is a sample review for '.$edition->edition_title,
                    'spoiler' => rand(0, 1),
                    'reread' => rand(0, 1),
                    'reviewed_at' => Carbon::now()->subDays(rand(0, 365)),
                ]);
            });
        }
    }
}
