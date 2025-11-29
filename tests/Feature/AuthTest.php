<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response as Http;
use Tests\TestCase;

class AuthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        $this->withoutExceptionHandling();
    }

    public function test_user_can_register_successfully(): void
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/auth/register', $payload)
            ->assertStatus(Http::HTTP_CREATED)
            ->assertJsonStructure([
                'message',
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);

        $this->assertNotNull($response->json('token'));
    }

    public function test_user_can_login_successfully(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $payload = [
            'email' => $user->email,
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/auth/login', $payload)
            ->assertStatus(Http::HTTP_OK)
            ->assertJsonStructure([
                'message',
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertEquals('Login successful.', $response->json('message'));
        $this->assertNotNull($response->json('token'));
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $payload = [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ];

        $this->postJson('/api/auth/login', $payload)
            ->assertStatus(Http::HTTP_FORBIDDEN)
            ->assertJson([
                'message' => 'Invalid credentials.',
            ]);
    }

    public function test_user_cannot_login_with_nonexistent_account(): void
    {
        $payload = [
            'email' => 'nouser@example.com',
            'password' => 'password123',
        ];

        $this->postJson('/api/auth/login', $payload)
            ->assertStatus(Http::HTTP_FORBIDDEN)
            ->assertJson([
                'message' => 'Invalid credentials.',
            ]);
    }

    public function test_authenticated_user_can_logout_successfully(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('token')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/auth/logout');

        $response->assertStatus(Http::HTTP_OK)
            ->assertJson([
                'message' => 'Logged out successfully',
            ]);
    }

}
