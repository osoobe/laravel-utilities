<?php

namespace Osoobe\Utilities\Tests\Traits;

use Osoobe\Utilities\Tests\Models\TestPost;
use Osoobe\Utilities\Tests\TestCase;

class HasVerifiedTest extends TestCase
{
    public function test_scopeVerified_returns_only_verified_posts(): void
    {
        TestPost::create(['title' => 'Verified', 'verified' => 1]);
        TestPost::create(['title' => 'Unverified', 'verified' => 0]);

        $results = TestPost::verified()->get();
        $this->assertCount(1, $results);
        $this->assertSame('Verified', $results->first()->title);
    }

    public function test_scopeNotVerified_returns_only_unverified_posts(): void
    {
        TestPost::create(['title' => 'Verified', 'verified' => 1]);
        TestPost::create(['title' => 'Unverified', 'verified' => 0]);

        $results = TestPost::notVerified()->get();
        $this->assertCount(1, $results);
        $this->assertSame('Unverified', $results->first()->title);
    }
}
