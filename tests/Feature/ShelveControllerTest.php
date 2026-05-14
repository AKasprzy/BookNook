<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookEdition;
use App\Models\Shelve;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response as Http;
use Tests\TestCase;

class ShelveControllerTest extends TestCase
{
    protected User $user;

    protected Shelve $shelve;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $book = Book::factory()->create();
        $edition = BookEdition::factory()->create(['book_id' => $book->id]);

        $this->shelve = Shelve::factory()->create([
            'user_id' => $this->user->id,
            'book_edition_id' => $edition->id,
        ]);
    }

    public function test_index_returns_paginated_shelves(): void
    {
        $this->actingAs($this->user)
            ->getJson('/api/shelves')
            ->assertStatus(Http::HTTP_OK)
            ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_show_returns_shelve(): void
    {
        $this->actingAs($this->user)
            ->getJson("/api/shelves/{$this->shelve->id}")
            ->assertStatus(Http::HTTP_OK);
    }

    public function test_guest_cannot_create_shelve(): void
    {
        $payload = Shelve::factory()->make()->toArray();

        $this->postJson('/api/shelves', $payload)
            ->assertStatus(Http::HTTP_UNAUTHORIZED);
    }

    public function test_authenticated_user_can_create_shelve(): void
    {
        $book = Book::factory()->create();
        $edition = BookEdition::factory()->create(['book_id' => $book->id]);

        $payload = [
            'book_edition_id' => $edition->id,
            'status' => 'reading',
            'times_read' => 1,
            'favourite' => false,
            'notes' => 'Test',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/shelves', $payload);

        $response->assertStatus(
            in_array($response->getStatusCode(), [
                Http::HTTP_CREATED,
                Http::HTTP_OK,
            ])
                ? $response->getStatusCode()
                : Http::HTTP_CREATED
        )->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'book_edition_id',
                'user_id',
                'status',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_user_can_update_and_delete_own_shelve(): void
    {
        $payload = ['status' => 'read'];

        $this->actingAs($this->user)
            ->putJson("/api/shelves/{$this->shelve->id}", $payload)
            ->assertStatus(Http::HTTP_OK);

        $this->actingAs($this->user)
            ->deleteJson("/api/shelves/{$this->shelve->id}")
            ->assertStatus(Http::HTTP_OK);
    }

    public function test_my_shelves_returns_only_authenticated_user_shelves(): void
    {
        $otherUser = User::factory()->create();
        $book = Book::factory()->create();
        $edition = BookEdition::factory()->create(['book_id' => $book->id]);

        Shelve::factory()->create([
            'user_id' => $otherUser->id,
            'book_edition_id' => $edition->id,
        ]);

        $this->actingAs($this->user)
            ->getJson('/api/my-shelves')
            ->assertStatus(Http::HTTP_OK)
            ->assertJsonCount(1, 'data');
    }

    public function test_my_shelves_by_status_returns_filtered_results(): void
    {
        $book = Book::factory()->create();
        $edition1 = BookEdition::factory()->create(['book_id' => $book->id]);
        $edition2 = BookEdition::factory()->create(['book_id' => $book->id]);

        Shelve::factory()->create([
            'user_id' => $this->user->id,
            'book_edition_id' => $edition1->id,
            'status' => 'read',
        ]);

        Shelve::factory()->create([
            'user_id' => $this->user->id,
            'book_edition_id' => $edition2->id,
            'status' => 'reading',
        ]);

        $this->actingAs($this->user)
            ->getJson('/api/my-shelves/status/read')
            ->assertStatus(Http::HTTP_OK)
            ->assertJsonCount(1, 'data');
    }
}
