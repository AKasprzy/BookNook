<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\Controllers\SearchController;
use PHPUnit\Framework\TestCase;

class SearchControllerParseDateTest extends TestCase
{
    private SearchController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new SearchController;
    }

    private function invokeParseDate(?string $date): string
    {
        $method = new \ReflectionMethod(SearchController::class, 'parseDate');

        return $method->invoke($this->controller, $date);
    }

    public function test_parse_date_returns_today_if_null(): void
    {
        $result = $this->invokeParseDate(null);
        $this->assertEquals(now()->toDateString(), $result);
    }

    public function test_parse_date_returns_year_only(): void
    {
        $result = $this->invokeParseDate('2020');
        $this->assertEquals('2020-01-01', $result);
    }

    public function test_parse_date_returns_year_month(): void
    {
        $result = $this->invokeParseDate('2020-05');
        $this->assertEquals('2020-05-01', $result);
    }

    public function test_parse_date_returns_full_date(): void
    {
        $result = $this->invokeParseDate('2020-05-20');
        $this->assertEquals('2020-05-20', $result);
    }
}
