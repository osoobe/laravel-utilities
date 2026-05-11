<?php

namespace Osoobe\Utilities\Tests\Traits;

use Carbon\Carbon;
use Osoobe\Utilities\Tests\Models\TestPost;
use Osoobe\Utilities\Tests\TestCase;

class HasEmailTest extends TestCase
{
    public function test_isEmailVerified_true_when_both_email_and_verified_at_set(): void
    {
        $post = TestPost::create([
            'email' => 'user@example.com',
            'email_verified_at' => Carbon::now(),
        ]);
        $this->assertTrue($post->isEmailVerified());
    }

    public function test_isEmailVerified_false_when_email_not_set(): void
    {
        $post = TestPost::create(['email_verified_at' => Carbon::now()]);
        $this->assertFalse($post->isEmailVerified());
    }

    public function test_isEmailVerified_false_when_email_verified_at_not_set(): void
    {
        $post = TestPost::create(['email' => 'user@example.com']);
        $this->assertFalse($post->isEmailVerified());
    }

    public function test_scopeEmailVerified_returns_posts_with_verified_at(): void
    {
        TestPost::create(['email' => 'a@b.com', 'email_verified_at' => Carbon::now()]);
        TestPost::create(['email' => 'c@d.com', 'email_verified_at' => null]);

        $results = TestPost::emailVerified()->get();
        $this->assertCount(1, $results);
        $this->assertSame('a@b.com', $results->first()->email);
    }

    public function test_scopeEmailNotVerified_returns_posts_without_verified_at(): void
    {
        TestPost::create(['email' => 'a@b.com', 'email_verified_at' => Carbon::now()]);
        TestPost::create(['email' => 'c@d.com', 'email_verified_at' => null]);

        $results = TestPost::emailNotVerified()->get();
        $this->assertCount(1, $results);
        $this->assertSame('c@d.com', $results->first()->email);
    }

    public function test_scopeEmail_finds_exact_email(): void
    {
        TestPost::create(['email' => 'find@me.com']);
        TestPost::create(['email' => 'other@me.com']);

        $results = TestPost::email('find@me.com')->get();
        $this->assertCount(1, $results);
        $this->assertSame('find@me.com', $results->first()->email);
    }

    public function test_scopeEmailVerifiedSince_includes_recently_verified(): void
    {
        // Post verified 10 days ago – within 30-day window
        TestPost::create([
            'email' => 'recent@me.com',
            'email_verified_at' => Carbon::now()->subDays(10),
        ]);
        // Post verified 60 days ago – outside 30-day window
        TestPost::create([
            'email' => 'old@me.com',
            'email_verified_at' => Carbon::now()->subDays(60),
        ]);

        // emailVerifiedSince uses created_at, so we need to create them with appropriate created_at
        // Re-test with created_at manipulation
        TestPost::truncate();

        $recent = new TestPost(['email' => 'recent@me.com', 'email_verified_at' => Carbon::now()]);
        $recent->created_at = Carbon::now()->subDays(10);
        $recent->updated_at = Carbon::now();
        $recent->save();

        $old = new TestPost(['email' => 'old@me.com', 'email_verified_at' => Carbon::now()]);
        $old->created_at = Carbon::now()->subDays(60);
        $old->updated_at = Carbon::now();
        $old->save();

        $results = TestPost::emailVerifiedSince(30)->get();
        $this->assertCount(1, $results);
        $this->assertSame('recent@me.com', $results->first()->email);
    }
}
