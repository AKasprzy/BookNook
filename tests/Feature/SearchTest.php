<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_guest_cannot_search(): void
    {
        $this->getJson('/api/search?q=test')
            ->assertStatus(HttpResponse::HTTP_UNAUTHORIZED);
    }

    public function test_search_fetches_google_results_stores_and_returns_top_five(): void
    {
        Http::fake([
            'https://www.googleapis.com/books/v1/volumes*' => Http::response([
                'items' => $this->fakeGoogleItems(),
            ], 200),
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/search?q=magic');

        $response->assertStatus(HttpResponse::HTTP_OK)
            ->assertJsonCount(5);

        $this->assertDatabaseCount('books', 5);
        $this->assertDatabaseCount('book_editions', 5);
        $this->assertDatabaseCount('genres', 2);
        $this->assertDatabaseCount('book_genre', 5);
    }

    public function test_search_does_not_duplicate_existing_books(): void
    {
        Http::fake([
            'https://www.googleapis.com/books/v1/volumes*' => Http::response([
                'items' => $this->fakeGoogleItems(),
            ], 200),
        ]);

        Sanctum::actingAs($this->user);

        $this->getJson('/api/search?q=magic');
        $this->getJson('/api/search?q=magic');

        $this->assertDatabaseCount('books', 5);
        $this->assertDatabaseCount('book_editions', 5);
    }

    private function fakeGoogleItems(): array
    {
        $items = [];

        for ($i = 1; $i <= 10; $i++) {
            $items[] = [
                'volumeInfo' => [
                    'title' => 'Book '.$i,
                    'authors' => ['Author '.$i],
                    'publishedDate' => '2020-01-01',
                    'description' => 'Desc '.$i,
                    'industryIdentifiers' => [
                        ['type' => 'ISBN_13', 'identifier' => '97800000000'.$i],
                    ],
                    'pageCount' => 100 + $i,
                    'imageLinks' => [
                        'thumbnail' => 'http://cover/'.$i,
                    ],
                    'publisher' => 'Pub '.$i,
                    'language' => 'en',
                    'ratingsCount' => 1000 - $i,
                    'categories' => [
                        'Fantasy / Magic',
                        'Adventure',
                    ],
                ],
            ];
        }

        return $items;
    }
}
