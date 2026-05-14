<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\Controllers\SearchController;
use PHPUnit\Framework\TestCase;

class SearchControllerNormalizeTest extends TestCase
{
    private SearchController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new SearchController;
    }

    private function invokeNormalize(array $item): ?array
    {
        $method = new \ReflectionMethod(SearchController::class, 'normalize');

        return $method->invoke($this->controller, $item);
    }

    public function test_normalize_returns_null_if_title_missing(): void
    {
        $item = ['volumeInfo' => ['authors' => ['Author']]];
        $this->assertNull($this->invokeNormalize($item));
    }

    public function test_normalize_returns_null_if_authors_missing(): void
    {
        $item = ['volumeInfo' => ['title' => 'Book']];
        $this->assertNull($this->invokeNormalize($item));
    }

    public function test_normalize_returns_null_if_no_isbn(): void
    {
        $item = ['volumeInfo' => ['title' => 'Book', 'authors' => ['Author']]];
        $this->assertNull($this->invokeNormalize($item));
    }

    public function test_normalize_returns_correct_array(): void
    {
        $item = [
            'volumeInfo' => [
                'title' => 'Book',
                'authors' => ['Author'],
                'industryIdentifiers' => [['type' => 'ISBN_13', 'identifier' => '9781234567897']],
                'language' => 'en',
                'publishedDate' => '2020-01-01',
                'description' => 'Desc',
                'pageCount' => 100,
                'imageLinks' => ['thumbnail' => 'url'],
                'publisher' => 'Pub',
                'categories' => ['Cat1'],
            ],
        ];

        $result = $this->invokeNormalize($item);

        $this->assertEquals('Book', $result['title']);
        $this->assertEquals('Author', $result['author']);
        $this->assertEquals('9781234567897', $result['isbn']);
        $this->assertEquals('en', $result['language']);
        $this->assertEquals('2020-01-01', $result['published']);
        $this->assertEquals('Desc', $result['description']);
        $this->assertEquals(100, $result['page_count']);
        $this->assertEquals('url', $result['cover']);
        $this->assertEquals('Pub', $result['publisher']);
        $this->assertEquals(['Cat1'], $result['categories']);
    }
}
