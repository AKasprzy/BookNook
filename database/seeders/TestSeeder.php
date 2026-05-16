<?php

namespace Database\Seeders;

use App\Enums\BookFormat;
use App\Enums\BookStatus;
use App\Enums\Role;
use App\Models\Book;
use App\Models\BookEdition;
use App\Models\Genre;
use App\Models\Motif;
use App\Models\Review;
use App\Models\Shelve;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role as SpatieRole;

class TestSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('TRUNCATE TABLE reviews RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE shelves RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE book_editions RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE books RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE genres RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE motifs RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE users RESTART IDENTITY CASCADE');

        foreach (Role::cases() as $role) {
            SpatieRole::firstOrCreate(['name' => $role->value]);
        }

        $user = User::create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->syncRoles(Role::User->value);

        $moderator = User::create([
            'name' => 'Moderator',
            'email' => 'moderator@test.com',
            'password' => Hash::make('password'),
        ]);

        $moderator->syncRoles(Role::Moderator->value);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);

        $admin->syncRoles(Role::Admin->value);

        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => Hash::make('password'),
        ]);

        $superAdmin->syncRoles(Role::SuperAdmin->value);

        $genre = Genre::create([
            'name' => 'Fantasy',
        ]);

        $motif = Motif::create([
            'name' => 'Found Family',
        ]);

        $book = Book::create([
            'title' => 'The Demo Book',
            'original_language' => 'en',
            'author' => 'Demo Author',
            'series' => 'Demo Series',
            'original_publication_date' => '2020-01-01',
        ]);

        $book->genres()->attach($genre->id);
        $book->motifs()->attach($motif->id);

        $edition = BookEdition::create([
            'book_id' => $book->id,
            'edition_title' => 'The Demo Book (Paperback)',
            'edition_publication_date' => '2020-06-01',
            'format' => BookFormat::cases()[0]->value,
            'edition_language' => 'en',
            'description' => 'A demo edition used for testing.',
            'isbn' => '9780000000001',
            'page_count' => 320,
            'length_minutes' => null,
            'cover_url' => null,
            'publisher' => 'Demo Publisher',
        ]);

        Review::create([
            'user_id' => $user->id,
            'book_edition_id' => $edition->id,
            'rating' => 5,
            'review_text' => 'This is a seeded demo review.',
            'spoiler' => false,
            'reread' => false,
            'reviewed_at' => Carbon::now()->subDays(3),
        ]);

        Shelve::create([
            'user_id' => $user->id,
            'book_edition_id' => $edition->id,
            'status' => BookStatus::cases()[0]->value,
            'times_read' => 1,
            'favourite' => true,
            'notes' => 'Seeded demo shelve entry.',
        ]);
    }
}
