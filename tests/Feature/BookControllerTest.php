<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response as Http;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    protected User $user;

    protected User $moderator;

    protected User $admin;

    protected User $superadmin;

    protected Book $book;

    protected array $books;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->moderator = User::factory()->moderator()->create();
        $this->admin = User::factory()->admin()->create();
        $this->superadmin = User::factory()->superAdmin()->create();

        $this->book = Book::factory()->create();
        $this->books = Book::factory()->count(20)->create()->all();
    }

    public function test_index_returns_paginated_books(): void
    {
        $response = $this->getJson('/api/books');

        $response->assertStatus(Http::HTTP_OK)->assertJsonStructure([
            'data',
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        ]);

        $this->assertCount(10, $response->json('data'));
    }

    public function test_index_respects_per_page_parameter(): void
    {
        $response = $this->getJson('/api/books?per_page=5');

        $response->assertStatus(Http::HTTP_OK);
        $this->assertCount(5, $response->json('data'));
        $this->assertEquals(5, $response->json('meta.last_page'));
    }

    public function test_show_returns_book(): void
    {
        $this->getJson("/api/books/{$this->book->id}")->assertStatus(Http::HTTP_OK);
        $this->actingAs($this->user)->getJson("/api/books/{$this->book->id}")->assertStatus(Http::HTTP_OK);
    }

    public function test_guest_cannot_create_book(): void
    {
        $payload = Book::factory()->make()->toArray();
        $this->postJson('/api/books', $payload)->assertStatus(Http::HTTP_UNAUTHORIZED);
    }

    public function test_authenticated_user_can_create_book(): void
    {
        $payload = Book::factory()->make()->toArray();
        $this->actingAs($this->user)->postJson('/api/books', $payload)->assertStatus(Http::HTTP_CREATED);
    }

    public function test_user_without_role_cannot_update_or_delete_book(): void
    {
        $payload = ['title' => 'Updated'];
        $this->actingAs($this->user)->putJson("/api/books/{$this->book->id}", $payload)->assertStatus(Http::HTTP_FORBIDDEN);
        $this->actingAs($this->user)->deleteJson("/api/books/{$this->book->id}")->assertStatus(Http::HTTP_FORBIDDEN);
    }

    public function test_moderator_can_update_and_delete_book(): void
    {
        $payload = ['title' => 'Moderator Updated'];
        $this->actingAs($this->moderator)->putJson("/api/books/{$this->book->id}", $payload)->assertStatus(Http::HTTP_OK);
        $this->actingAs($this->moderator)->deleteJson("/api/books/{$this->book->id}")->assertStatus(Http::HTTP_OK);
    }

    public function test_admin_can_update_and_delete_book(): void
    {
        $payload = ['title' => 'Admin Updated'];
        $this->actingAs($this->admin)->putJson("/api/books/{$this->book->id}", $payload)->assertStatus(Http::HTTP_OK);
        $this->actingAs($this->admin)->deleteJson("/api/books/{$this->book->id}")->assertStatus(Http::HTTP_OK);
    }

    public function test_superadmin_can_update_and_delete_book(): void
    {
        $payload = ['title' => 'Superadmin Updated'];
        $this->actingAs($this->superadmin)->putJson("/api/books/{$this->book->id}", $payload)->assertStatus(Http::HTTP_OK);
        $this->actingAs($this->superadmin)->deleteJson("/api/books/{$this->book->id}")->assertStatus(Http::HTTP_OK);
    }
}
