<?php

namespace Osoobe\Utilities\Tests\Traits;

use Osoobe\Utilities\Tests\Models\TestPost;
use Osoobe\Utilities\Tests\TestCase;

class ActiveTest extends TestCase
{
    public function test_scopeActive_returns_only_active_posts(): void
    {
        TestPost::create(['title' => 'Active', 'is_active' => 1]);
        TestPost::create(['title' => 'Inactive', 'is_active' => 0]);

        $results = TestPost::active()->get();
        $this->assertCount(1, $results);
        $this->assertSame('Active', $results->first()->title);
    }

    public function test_scopeNotActive_returns_only_inactive_posts(): void
    {
        TestPost::create(['title' => 'Active', 'is_active' => 1]);
        TestPost::create(['title' => 'Inactive', 'is_active' => 0]);

        $results = TestPost::notActive()->get();
        $this->assertCount(1, $results);
        $this->assertSame('Inactive', $results->first()->title);
    }

    public function test_scopeHidden_returns_only_hidden_posts(): void
    {
        TestPost::create(['title' => 'Visible', 'hidden' => 0]);
        TestPost::create(['title' => 'Hidden', 'hidden' => 1]);

        $results = TestPost::hidden()->get();
        $this->assertCount(1, $results);
        $this->assertSame('Hidden', $results->first()->title);
    }
}
