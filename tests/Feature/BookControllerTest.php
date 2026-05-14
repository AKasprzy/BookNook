<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Role;
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

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->moderator = User::factory()->create();
        $this->moderator->assignRole(Role::Moderator->value);

        $this->admin = User::factory()->create();
        $this->admin->assignRole(Role::Admin->value);

        $this->superadmin = User::factory()->create();
        $this->superadmin->assignRole(Role::SuperAdmin->value);

        $this->book = Book::factory()->create();
        Book::factory()->count(20)->create();
    }

    private function validBookPayload(): array
    {
        return [
            'title' => 'Test Book',
            'original_language' => 'en',
            'author' => 'Test Author',
            'original_publication_date' => '2020-01-01',
            'series' => null,
            'edition' => [
                'edition_title' => 'First Edition',
                'edition_publication_date' => '2020-01-01',
                'format' => 'print',
                'edition_language' => 'en',
                'description' => null,
                'isbn' => null,
                'page_count' => null,
                'length_minutes' => null,
                'cover_url' => null,
                'publisher' => null,
            ],
        ];
    }

    public function test_show_returns_book(): void
    {
        $this->getJson("/api/books/{$this->book->id}")
            ->assertStatus(Http::HTTP_OK);

        $this->actingAs($this->user)
            ->getJson("/api/books/{$this->book->id}")
            ->assertStatus(Http::HTTP_OK);
    }

    public function test_guest_cannot_create_book(): void
    {
        $this->postJson('/api/books', $this->validBookPayload())
            ->assertStatus(Http::HTTP_UNAUTHORIZED);
    }

    public function test_authenticated_user_can_create_book(): void
    {
        $this->actingAs($this->user)
            ->postJson('/api/books', $this->validBookPayload())
            ->assertStatus(Http::HTTP_CREATED);
    }

    public function test_user_without_role_cannot_update_or_delete_book(): void
    {
        $payload = ['title' => 'Updated'];

        $this->actingAs($this->user)
            ->putJson("/api/books/{$this->book->id}", $payload)
            ->assertStatus(Http::HTTP_FORBIDDEN);

        $this->actingAs($this->user)
            ->deleteJson("/api/books/{$this->book->id}")
            ->assertStatus(Http::HTTP_FORBIDDEN);
    }

    public function test_moderator_cannot_update_or_delete_book(): void
    {
        $payload = ['title' => 'Moderator Try'];

        $this->actingAs($this->moderator)
            ->putJson("/api/books/{$this->book->id}", $payload)
            ->assertStatus(Http::HTTP_FORBIDDEN);

        $this->actingAs($this->moderator)
            ->deleteJson("/api/books/{$this->book->id}")
            ->assertStatus(Http::HTTP_FORBIDDEN);
    }

    public function test_admin_can_update_and_delete_book(): void
    {
        $payload = ['title' => 'Admin Updated'];

        $this->actingAs($this->admin)
            ->putJson("/api/books/{$this->book->id}", $payload)
            ->assertStatus(Http::HTTP_OK);

        $this->actingAs($this->admin)
            ->deleteJson("/api/books/{$this->book->id}")
            ->assertStatus(Http::HTTP_OK);
    }

    public function test_superadmin_can_update_and_delete_book(): void
    {
        $payload = ['title' => 'Superadmin Updated'];

        $this->actingAs($this->superadmin)
            ->putJson("/api/books/{$this->book->id}", $payload)
            ->assertStatus(Http::HTTP_OK);

        $this->actingAs($this->superadmin)
            ->deleteJson("/api/books/{$this->book->id}")
            ->assertStatus(Http::HTTP_OK);
    }
}
