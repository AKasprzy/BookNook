<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response as Http;
use Tests\TestCase;

class ReviewControllerTest extends TestCase
{
    protected User $user;

    protected Review $review;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->review = Review::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_index_returns_paginated_reviews(): void
    {
        $this->actingAs($this->user)
            ->getJson('/api/reviews')
            ->assertStatus(Http::HTTP_OK)
            ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_show_returns_review(): void
    {
        $this->actingAs($this->user)
            ->getJson("/api/reviews/{$this->review->id}")
            ->assertStatus(Http::HTTP_OK);
    }

    public function test_guest_cannot_create_review(): void
    {
        $payload = Review::factory()->make()->toArray();

        $this->postJson('/api/reviews', $payload)
            ->assertStatus(Http::HTTP_UNAUTHORIZED);
    }

    public function test_authenticated_user_can_create_review(): void
    {
        $book = Book::factory()->create();
        $payload = Review::factory()->make([
            'book_id' => $book->id,
            'user_id' => $this->user->id,
        ])->toArray();

        $this->actingAs($this->user)
            ->postJson('/api/reviews', $payload)
            ->assertStatus(Http::HTTP_CREATED)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id', 'book_id', 'user_id', 'rating', 'review_text',
                    'spoiler', 'reread', 'created_at', 'updated_at',
                ],
            ]);
    }

    public function test_user_can_update_and_delete_own_review(): void
    {
        $payload = ['review_text' => 'Updated review text'];

        $this->actingAs($this->user)
            ->putJson("/api/reviews/{$this->review->id}", $payload)
            ->assertStatus(Http::HTTP_OK);

        $this->actingAs($this->user)
            ->deleteJson("/api/reviews/{$this->review->id}")
            ->assertStatus(Http::HTTP_OK);
    }
}
