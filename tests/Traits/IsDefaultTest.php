<?php

namespace Osoobe\Utilities\Tests\Traits;

use Osoobe\Utilities\Tests\Models\TestPost;
use Osoobe\Utilities\Tests\TestCase;

class IsDefaultTest extends TestCase
{
    public function test_scopeIsDefault_returns_only_default_posts(): void
    {
        TestPost::create(['title' => 'Default', 'is_default' => 1]);
        TestPost::create(['title' => 'Not Default', 'is_default' => 0]);

        $results = TestPost::isDefault()->get();
        $this->assertCount(1, $results);
        $this->assertSame('Default', $results->first()->title);
    }

    public function test_scopeNotDefault_returns_non_default_posts(): void
    {
        TestPost::create(['title' => 'Default', 'is_default' => 1]);
        TestPost::create(['title' => 'Not Default', 'is_default' => 0]);

        $results = TestPost::notDefault()->get();
        $this->assertCount(1, $results);
        $this->assertSame('Not Default', $results->first()->title);
    }
}
