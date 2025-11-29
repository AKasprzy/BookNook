<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookEdition;
use App\Models\Review;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response as Http;
use Tests\TestCase;

class BookEditionControllerTest extends TestCase
{
    protected User $user;

    protected User $moderator;

    protected User $admin;

    protected User $superadmin;

    protected BookEdition $edition;

    protected array $editions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->edition = BookEdition::factory()->create();
        $this->editions = BookEdition::factory()->count(10)->create()->all();

        $this->user = User::factory()->create();
        $this->moderator = User::factory()->moderator()->create();
        $this->admin = User::factory()->admin()->create();
        $this->superadmin = User::factory()->superAdmin()->create();
    }

    public function test_index_returns_paginated_book_editions(): void
    {
        $response = $this->getJson('/api/book-editions');

        $response->assertStatus(Http::HTTP_OK)
            ->assertJsonStructure([
                'data',
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);

        $this->assertCount(10, $response->json('data'));
    }

    public function test_show_returns_book_edition(): void
    {
        $this->getJson("/api/book-editions/{$this->edition->id}")
            ->assertStatus(Http::HTTP_OK);

        $this->actingAs($this->user)
            ->getJson("/api/book-editions/{$this->edition->id}")
            ->assertStatus(Http::HTTP_OK);
    }

    public function test_guest_cannot_create_book_edition(): void
    {
        $payload = BookEdition::factory()->make()->toArray();

        $this->postJson('/api/book-editions', $payload)
            ->assertStatus(Http::HTTP_UNAUTHORIZED);
    }

    public function test_authenticated_user_can_create_book_edition(): void
    {
        $book = Book::factory()->create();
        $payload = BookEdition::factory()->make(['book_id' => $book->id])->toArray();

        $this->actingAs($this->user)
            ->postJson('/api/book-editions', $payload)
            ->assertStatus(Http::HTTP_CREATED)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'edition_title',
                    'edition_publication_date',
                    'format',
                    'edition_language',
                    'description',
                    'isbn',
                    'page_count',
                    'length_minutes',
                    'cover_url',
                    'publisher',
                    'average_rating',
                    'book' => [
                        'id',
                        'title',
                        'author',
                        'series',
                        'original_publication_date',
                        'original_language',
                        'created_at',
                        'updated_at',
                    ],
                    'genres',
                    'motifs',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertDatabaseHas('book_editions', ['book_id' => $book->id]);
    }

    public function test_user_without_role_cannot_update_or_delete_book_edition(): void
    {
        $payload = ['edition_title' => 'Updated'];

        $this->actingAs($this->user)
            ->putJson("/api/book-editions/{$this->edition->id}", $payload)
            ->assertStatus(Http::HTTP_FORBIDDEN);

        $this->actingAs($this->user)
            ->deleteJson("/api/book-editions/{$this->edition->id}")
            ->assertStatus(Http::HTTP_FORBIDDEN);
    }

    public function test_moderator_can_update_and_delete_book_edition(): void
    {
        $payload = ['edition_title' => 'Moderator Updated'];

        $this->actingAs($this->moderator)
            ->putJson("/api/book-editions/{$this->edition->id}", $payload)
            ->assertStatus(Http::HTTP_OK);

        $this->actingAs($this->moderator)
            ->deleteJson("/api/book-editions/{$this->edition->id}")
            ->assertStatus(Http::HTTP_OK);
    }

    public function test_admin_can_update_and_delete_book_edition(): void
    {
        $payload = ['edition_title' => 'Admin Updated'];

        $this->actingAs($this->admin)
            ->putJson("/api/book-editions/{$this->edition->id}", $payload)
            ->assertStatus(Http::HTTP_OK);

        $this->actingAs($this->admin)
            ->deleteJson("/api/book-editions/{$this->edition->id}")
            ->assertStatus(Http::HTTP_OK);
    }

    public function test_superadmin_can_update_and_delete_book_edition(): void
    {
        $payload = ['edition_title' => 'Superadmin Updated'];

        $this->actingAs($this->superadmin)
            ->putJson("/api/book-editions/{$this->edition->id}", $payload)
            ->assertStatus(Http::HTTP_OK);

        $this->actingAs($this->superadmin)
            ->deleteJson("/api/book-editions/{$this->edition->id}")
            ->assertStatus(Http::HTTP_OK);
    }

    public function test_average_rating_is_calculated_from_reviews(): void
    {
        $book = Book::factory()->create();
        $edition = BookEdition::factory()->create(['book_id' => $book->id]);

        Review::factory()->create(['book_id' => $book->id, 'rating' => 5]);
        Review::factory()->create(['book_id' => $book->id, 'rating' => 3]);
        Review::factory()->create(['book_id' => $book->id, 'rating' => 4]);

        $expectedAverage = (float) Review::where('book_id', $book->id)->avg('rating');
        $response = $this->getJson("/api/book-editions/{$edition->id}")
            ->assertStatus(Http::HTTP_OK);

        $this->assertEquals(round($expectedAverage, 1), $response->json('data.average_rating'));
    }

    public function test_average_rating_is_null_when_no_reviews(): void
    {
        $book = Book::factory()->create();
        $edition = BookEdition::factory()->create(['book_id' => $book->id]);

        $this->getJson("/api/book-editions/{$edition->id}")
            ->assertStatus(Http::HTTP_OK)
            ->assertJsonPath('data.average_rating', null);
    }

    public function test_average_rating_changes_when_new_review_is_added(): void
    {
        $book = Book::factory()->create();
        $edition = BookEdition::factory()->create(['book_id' => $book->id]);

        Review::factory()->create(['book_id' => $book->id, 'rating' => 5]);
        Review::factory()->create(['book_id' => $book->id, 'rating' => 3]);

        $initialAverage = (float) Review::where('book_id', $book->id)->avg('rating');
        $this->assertEquals(4.0, round($initialAverage, 1));

        $response = $this->getJson("/api/book-editions/{$edition->id}")
            ->assertStatus(Http::HTTP_OK);
        $this->assertEquals(round($initialAverage, 1), $response->json('data.average_rating'));

        Review::factory()->create(['book_id' => $book->id, 'rating' => 1]);

        $newAverage = (float) Review::where('book_id', $book->id)->avg('rating');
        $this->assertEquals(3.0, round($newAverage, 1));

        $response = $this->getJson("/api/book-editions/{$edition->id}")
            ->assertStatus(Http::HTTP_OK);
        $this->assertEquals(round($newAverage, 1), $response->json('data.average_rating'));
    }
}
