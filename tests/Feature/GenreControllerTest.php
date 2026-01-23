<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response as Http;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    protected User $user;

    protected User $moderator;

    protected User $admin;

    protected User $superadmin;

    protected Genre $genre;

    protected array $genres;

    protected function setUp(): void
    {
        parent::setUp();

        $this->genre = Genre::factory()->create();
        $this->genres = Genre::factory()->count(10)->create()->all();

        $this->user = User::factory()->create();
        $this->moderator = User::factory()->moderator()->create();
        $this->admin = User::factory()->admin()->create();
        $this->superadmin = User::factory()->superAdmin()->create();
    }

    public function test_index_returns_genres(): void
    {
        $response = $this->getJson('/api/genres');

        $response->assertStatus(Http::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name'],
                ],
            ]);
    }

    public function test_show_returns_genre(): void
    {
        $this->getJson("/api/genres/{$this->genre->id}")
            ->assertStatus(Http::HTTP_OK)
            ->assertJsonStructure(['data' => ['id', 'name']]);
    }

    public function test_guest_cannot_create_genre(): void
    {
        $payload = Genre::factory()->make()->toArray();

        $this->postJson('/api/genres', $payload)
            ->assertStatus(Http::HTTP_UNAUTHORIZED);
    }

    public function test_user_without_role_cannot_create_update_or_delete_genre(): void
    {
        $payload = ['name' => 'Unauthorized Update'];

        $this->actingAs($this->user)
            ->postJson('/api/genres', $payload)
            ->assertStatus(Http::HTTP_FORBIDDEN);

        $this->actingAs($this->user)
            ->putJson("/api/genres/{$this->genre->id}", $payload)
            ->assertStatus(Http::HTTP_FORBIDDEN);

        $this->actingAs($this->user)
            ->deleteJson("/api/genres/{$this->genre->id}")
            ->assertStatus(Http::HTTP_FORBIDDEN);
    }

    public function test_admin_can_create_update_and_delete_genre(): void
    {
        $payload = Genre::factory()->make()->toArray();

        $this->actingAs($this->admin)
            ->postJson('/api/genres', $payload)
            ->assertStatus(Http::HTTP_CREATED);

        $this->actingAs($this->admin)
            ->putJson("/api/genres/{$this->genre->id}", ['name' => 'Admin Updated'])
            ->assertStatus(Http::HTTP_OK);

        $this->actingAs($this->admin)
            ->deleteJson("/api/genres/{$this->genre->id}")
            ->assertStatus(Http::HTTP_OK);
    }

    public function test_superadmin_can_create_update_and_delete_genre(): void
    {
        $payload = Genre::factory()->make()->toArray();

        $this->actingAs($this->superadmin)
            ->postJson('/api/genres', $payload)
            ->assertStatus(Http::HTTP_CREATED);

        $this->actingAs($this->superadmin)
            ->putJson("/api/genres/{$this->genre->id}", ['name' => 'Superadmin Updated'])
            ->assertStatus(Http::HTTP_OK);

        $this->actingAs($this->superadmin)
            ->deleteJson("/api/genres/{$this->genre->id}")
            ->assertStatus(Http::HTTP_OK);
    }
}
