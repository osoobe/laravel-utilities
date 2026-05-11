<?php

namespace Osoobe\Utilities\Tests\Traits;

use Osoobe\Utilities\Tests\Models\TestPost;
use Osoobe\Utilities\Tests\TestCase;

class SortedTest extends TestCase
{
    public function test_scopeSorted_returns_posts_in_ascending_sort_order(): void
    {
        TestPost::create(['title' => 'Third', 'sort_order' => 3]);
        TestPost::create(['title' => 'First', 'sort_order' => 1]);
        TestPost::create(['title' => 'Second', 'sort_order' => 2]);

        $results = TestPost::sorted()->get();
        $this->assertCount(3, $results);
        $this->assertSame('First', $results[0]->title);
        $this->assertSame('Second', $results[1]->title);
        $this->assertSame('Third', $results[2]->title);
    }
}
