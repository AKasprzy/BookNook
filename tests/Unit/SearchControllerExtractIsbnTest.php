<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\Controllers\SearchController;
use PHPUnit\Framework\TestCase;

class SearchControllerExtractIsbnTest extends TestCase
{
    private SearchController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new SearchController;
    }

    private function invokeExtractIsbn(array $ids): ?string
    {
        $method = new \ReflectionMethod(SearchController::class, 'extractIsbn');

        return $method->invoke($this->controller, $ids);
    }

    public function test_extract_isbn_returns_isbn13(): void
    {
        $ids = [
            ['type' => 'ISBN_10', 'identifier' => '1234567890'],
            ['type' => 'ISBN_13', 'identifier' => '9781234567897'],
        ];

        $this->assertEquals('9781234567897', $this->invokeExtractIsbn($ids));
    }

    public function test_extract_isbn_returns_null_if_no_isbn13(): void
    {
        $ids = [
            ['type' => 'ISBN_10', 'identifier' => '1234567890'],
        ];

        $this->assertNull($this->invokeExtractIsbn($ids));
    }

    public function test_extract_isbn_returns_null_if_empty_array(): void
    {
        $this->assertNull($this->invokeExtractIsbn([]));
    }
}
