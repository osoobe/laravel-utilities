<?php

namespace Osoobe\Utilities\Tests\Traits;

use Osoobe\Utilities\Tests\Models\TestPost;
use Osoobe\Utilities\Tests\TestCase;

class AdvanceWhereQueryTest extends TestCase
{
    public function test_scopeWhereKeyOrNull_with_non_empty_value_filters(): void
    {
        TestPost::create(['title' => 'Alpha']);
        TestPost::create(['title' => 'Beta']);

        $results = TestPost::whereKeyOrNull('title', 'Alpha')->get();
        $this->assertCount(1, $results);
        $this->assertSame('Alpha', $results->first()->title);
    }

    public function test_scopeWhereKeyOrNull_with_null_returns_all(): void
    {
        TestPost::create(['title' => 'Alpha']);
        TestPost::create(['title' => 'Beta']);

        $results = TestPost::whereKeyOrNull('title', null)->get();
        $this->assertCount(2, $results);
    }

    public function test_scopeWhereKeyOrNull_with_empty_string_returns_all(): void
    {
        TestPost::create(['title' => 'Alpha']);
        TestPost::create(['title' => 'Beta']);

        $results = TestPost::whereKeyOrNull('title', '')->get();
        $this->assertCount(2, $results);
    }

    public function test_scopeWhereKeyOrNull_with_zero_returns_all(): void
    {
        TestPost::create(['title' => 'Alpha', 'sort_order' => 1]);
        TestPost::create(['title' => 'Beta', 'sort_order' => 2]);

        // 0 is falsy via empty(), so no WHERE is applied
        $results = TestPost::whereKeyOrNull('sort_order', 0)->get();
        $this->assertCount(2, $results);
    }
}
