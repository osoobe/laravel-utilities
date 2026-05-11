<?php

namespace Osoobe\Utilities\Tests\Traits;

use Carbon\Carbon;
use Osoobe\Utilities\Tests\Models\TestPost;
use Osoobe\Utilities\Tests\TestCase;

class TimeDiffTest extends TestCase
{
    public function test_created_time_diff_returns_non_empty_string(): void
    {
        $post = TestPost::create(['title' => 'Post']);
        $this->assertNotEmpty($post->created_time_diff);
        $this->assertIsString($post->created_time_diff);
    }

    public function test_posted_time_diff_returns_non_empty_string(): void
    {
        $post = TestPost::create(['title' => 'Post']);
        $this->assertNotEmpty($post->posted_time_diff);
        $this->assertIsString($post->posted_time_diff);
    }

    public function test_expiry_date_time_diff_with_expiry_date_set(): void
    {
        $post = TestPost::create([
            'title' => 'Post',
            'expiry_date' => Carbon::now()->addDays(10),
        ]);
        $diff = $post->expiry_date_time_diff;
        $this->assertNotEmpty($diff);
        $this->assertIsString($diff);
        $this->assertNotSame('Non-Disclosure ', $diff);
    }

    public function test_expiry_date_time_diff_with_null_expiry_date(): void
    {
        $post = TestPost::create(['title' => 'Post', 'expiry_date' => null]);
        $this->assertSame('Non-Disclosure ', $post->expiry_date_time_diff);
    }

    public function test_isExpired_on_expired_post_returns_true(): void
    {
        $post = TestPost::create([
            'title' => 'Expired',
            'expiry_date' => Carbon::now()->subDays(1),
        ]);
        $this->assertTrue($post->isExpired());
    }

    public function test_isExpired_on_non_expired_post_returns_false(): void
    {
        $post = TestPost::create([
            'title' => 'Future',
            'expiry_date' => Carbon::now()->addDays(5),
        ]);
        $this->assertFalse($post->isExpired());
    }

    public function test_isExpired_on_null_expiry_date_returns_non_disclosure(): void
    {
        $post = TestPost::create(['title' => 'No Expiry', 'expiry_date' => null]);
        $this->assertSame('Non-Disclosure ', $post->isExpired());
    }

    public function test_recentlyCreated_freshly_created_returns_true(): void
    {
        $post = TestPost::create(['title' => 'New']);
        $this->assertTrue($post->recentlyCreated(1));
    }

    public function test_recentlyUpdated_freshly_saved_returns_true(): void
    {
        $post = TestPost::create(['title' => 'Post']);
        $post->title = 'Updated';
        $post->save();
        $this->assertTrue($post->recentlyUpdated(1, 'subHours'));
    }

    public function test_scopeCreatedToday_includes_post_created_now(): void
    {
        TestPost::create(['title' => 'Today']);
        $results = TestPost::createdToday()->get();
        $this->assertCount(1, $results);
    }

    public function test_scopeCreatedSinceWeek_includes_post_from_3_days_ago(): void
    {
        $post = new TestPost(['title' => '3 Days Ago']);
        $post->created_at = Carbon::now()->subDays(3);
        $post->updated_at = Carbon::now();
        $post->save();

        $results = TestPost::createdSinceWeek()->get();
        $this->assertCount(1, $results);
    }

    public function test_scopeCreatedSinceWeek_excludes_post_from_10_days_ago(): void
    {
        $post = new TestPost(['title' => '10 Days Ago']);
        $post->created_at = Carbon::now()->subDays(10);
        $post->updated_at = Carbon::now();
        $post->save();

        $results = TestPost::createdSinceWeek()->get();
        $this->assertCount(0, $results);
    }

    public function test_scopeRecentlyCreated_includes_post_from_2_days_ago(): void
    {
        $post = new TestPost(['title' => '2 Days Ago']);
        $post->created_at = Carbon::now()->subDays(2);
        $post->updated_at = Carbon::now();
        $post->save();

        // scopeRecentlyCreated is the Eloquent scope (callable as query scope)
        $results = TestPost::query()->recentlyCreated(3)->get();
        $this->assertCount(1, $results);
    }

    public function test_scopeRecentlyCreated_excludes_post_from_5_days_ago(): void
    {
        $post = new TestPost(['title' => '5 Days Ago']);
        $post->created_at = Carbon::now()->subDays(5);
        $post->updated_at = Carbon::now();
        $post->save();

        $results = TestPost::query()->recentlyCreated(3)->get();
        $this->assertCount(0, $results);
    }

    public function test_scopeRecentlyCreated_subWeeks_includes_post_from_1_week_ago(): void
    {
        $post = new TestPost(['title' => '1 Week Ago']);
        $post->created_at = Carbon::now()->subWeeks(1);
        $post->updated_at = Carbon::now();
        $post->save();

        $results = TestPost::query()->recentlyCreated(2, 'subWeeks')->get();
        $this->assertCount(1, $results);
    }

    public function test_scopeBetweenDates_includes_post_within_range(): void
    {
        $post = new TestPost(['title' => 'In Range']);
        $post->created_at = Carbon::now()->subDays(3);
        $post->updated_at = Carbon::now();
        $post->save();

        $start = Carbon::now()->subDays(7);
        $end = Carbon::now();
        $results = TestPost::betweenDates('created_at', $start, $end)->get();
        $this->assertCount(1, $results);
    }

    public function test_scopeBetweenDates_excludes_post_outside_range(): void
    {
        $post = new TestPost(['title' => 'Out of Range']);
        $post->created_at = Carbon::now()->subDays(30);
        $post->updated_at = Carbon::now();
        $post->save();

        $start = Carbon::now()->subDays(7);
        $end = Carbon::now();
        $results = TestPost::betweenDates('created_at', $start, $end)->get();
        $this->assertCount(0, $results);
    }

    public function test_scopeExpired_on_fresh_query_without_expiry_date_unmodified(): void
    {
        TestPost::create(['title' => 'No Expiry', 'expiry_date' => null]);
        TestPost::create(['title' => 'With Expiry', 'expiry_date' => Carbon::now()->subDays(1)]);

        // On fresh query, scopeExpired checks $this->expiry_date on prototype model instance
        // which will be null, so no WHERE clause is added – all records returned
        $results = TestPost::expired()->get();
        $this->assertCount(2, $results);
    }

    public function test_scopeNotExpired_on_fresh_query_without_expiry_date_unmodified(): void
    {
        TestPost::create(['title' => 'Post 1']);
        TestPost::create(['title' => 'Post 2']);

        // Same behavior: no WHERE clause added on fresh query
        $results = TestPost::notExpired()->get();
        $this->assertCount(2, $results);
    }
}
