<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Motif;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response as Http;
use Tests\TestCase;

class MotifControllerTest extends TestCase
{
    protected User $user;

    protected User $moderator;

    protected User $admin;

    protected User $superadmin;

    protected Motif $motif;

    protected array $motifs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->motif = Motif::factory()->create();
        $this->motifs = Motif::factory()->count(10)->create()->all();

        $this->user = User::factory()->create();
        $this->moderator = User::factory()->moderator()->create();
        $this->admin = User::factory()->admin()->create();
        $this->superadmin = User::factory()->superAdmin()->create();
    }

    public function test_index_returns_motifs(): void
    {
        $response = $this->getJson('/api/motifs');

        $response->assertStatus(Http::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name'],
                ],
            ]);
    }

    public function test_show_returns_motif(): void
    {
        $this->getJson("/api/motifs/{$this->motif->id}")
            ->assertStatus(Http::HTTP_OK)
            ->assertJsonStructure(['data' => ['id', 'name']]);
    }

    public function test_guest_cannot_create_motif(): void
    {
        $payload = Motif::factory()->make()->toArray();

        $this->postJson('/api/motifs', $payload)
            ->assertStatus(Http::HTTP_UNAUTHORIZED);
    }

    public function test_user_without_role_cannot_create_update_or_delete_motif(): void
    {
        $payload = ['name' => 'Unauthorized Update'];

        $this->actingAs($this->user)
            ->postJson('/api/motifs', $payload)
            ->assertStatus(Http::HTTP_FORBIDDEN);

        $this->actingAs($this->user)
            ->putJson("/api/motifs/{$this->motif->id}", $payload)
            ->assertStatus(Http::HTTP_FORBIDDEN);

        $this->actingAs($this->user)
            ->deleteJson("/api/motifs/{$this->motif->id}")
            ->assertStatus(Http::HTTP_FORBIDDEN);
    }

    public function test_moderator_can_create_update_and_delete_motif(): void
    {
        $payload = Motif::factory()->make()->toArray();

        $this->actingAs($this->moderator)
            ->postJson('/api/motifs', $payload)
            ->assertStatus(Http::HTTP_CREATED);

        $this->actingAs($this->moderator)
            ->putJson("/api/motifs/{$this->motif->id}", ['name' => 'Moderator Updated'])
            ->assertStatus(Http::HTTP_OK);

        $this->actingAs($this->moderator)
            ->deleteJson("/api/motifs/{$this->motif->id}")
            ->assertStatus(Http::HTTP_OK);
    }

    public function test_admin_can_create_update_and_delete_motif(): void
    {
        $payload = Motif::factory()->make()->toArray();

        $this->actingAs($this->admin)
            ->postJson('/api/motifs', $payload)
            ->assertStatus(Http::HTTP_CREATED);

        $this->actingAs($this->admin)
            ->putJson("/api/motifs/{$this->motif->id}", ['name' => 'Admin Updated'])
            ->assertStatus(Http::HTTP_OK);

        $this->actingAs($this->admin)
            ->deleteJson("/api/motifs/{$this->motif->id}")
            ->assertStatus(Http::HTTP_OK);
    }

    public function test_superadmin_can_create_update_and_delete_motif(): void
    {
        $payload = Motif::factory()->make()->toArray();

        $this->actingAs($this->superadmin)
            ->postJson('/api/motifs', $payload)
            ->assertStatus(Http::HTTP_CREATED);

        $this->actingAs($this->superadmin)
            ->putJson("/api/motifs/{$this->motif->id}", ['name' => 'Superadmin Updated'])
            ->assertStatus(Http::HTTP_OK);

        $this->actingAs($this->superadmin)
            ->deleteJson("/api/motifs/{$this->motif->id}")
            ->assertStatus(Http::HTTP_OK);
    }
}
