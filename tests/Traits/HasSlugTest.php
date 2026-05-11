<?php

namespace Osoobe\Utilities\Tests\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Osoobe\Utilities\Tests\Models\TestPost;
use Osoobe\Utilities\Tests\TestCase;

class HasSlugTest extends TestCase
{
    public function test_findBySlugOrFail_finds_by_slug(): void
    {
        $post = TestPost::create(['title' => 'Slugged', 'slug' => 'my-slug']);
        $found = TestPost::findBySlugOrFail('my-slug');
        $this->assertSame($post->id, $found->id);
    }

    public function test_findBySlugOrFail_finds_by_id_when_numeric(): void
    {
        $post = TestPost::create(['title' => 'By ID']);
        $found = TestPost::findBySlugOrFail($post->id);
        $this->assertSame($post->id, $found->id);
    }

    public function test_findBySlugOrFail_throws_not_found_for_nonexistent(): void
    {
        $this->expectException(ModelNotFoundException::class);
        TestPost::findBySlugOrFail('nonexistent-slug');
    }
}
