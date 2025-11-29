<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Book;
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
        $this->shelve = Shelve::factory()->create(['user_id' => $this->user->id]);
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
        $payload = Shelve::factory()->make([
            'book_id' => $book->id,
            'user_id' => $this->user->id,
        ])->toArray();

        $this->actingAs($this->user)
            ->postJson('/api/shelves', $payload)
            ->assertStatus(Http::HTTP_CREATED)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id', 'book_id', 'user_id', 'status', 'created_at', 'updated_at',
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
}
