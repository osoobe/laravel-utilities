<?php

namespace Osoobe\Utilities\Tests\Traits;

use Osoobe\Utilities\Tests\Models\TestFtsPost;
use Osoobe\Utilities\Tests\Models\TestHftsPost;
use Osoobe\Utilities\Tests\TestCase;

class FullTextSearchTraitTest extends TestCase
{
    public function test_buildFullTextSearch_contains_MATCH_and_AGAINST(): void
    {
        $sql = TestFtsPost::buildFullTextSearch(['title', 'body'], 'hello');
        $this->assertStringContainsString('MATCH', $sql);
        $this->assertStringContainsString('AGAINST', $sql);
        $this->assertStringContainsString('hello', $sql);
    }

    public function test_buildFullTextSearch_bool_mode(): void
    {
        $sql = TestFtsPost::buildFullTextSearch(['title', 'body'], 'hello', true);
        $this->assertStringContainsString('IN BOOLEAN MODE', $sql);
    }

    public function test_buildFullTextSearch_strips_special_characters(): void
    {
        $sql = TestFtsPost::buildFullTextSearch(['title'], 'he@llo!');
        // Special chars @, ! are replaced by spaces — not removed entirely
        $this->assertStringNotContainsString('@', $sql);
        $this->assertStringNotContainsString('!', $sql);
        // 'he' and 'llo' are present (separated by space from the replacements)
        $this->assertStringContainsString('he', $sql);
        $this->assertStringContainsString('llo', $sql);
    }

    public function test_selectFTSScore_ends_with_as_fts_score(): void
    {
        $result = TestFtsPost::selectFTSScore(['title'], 'foo');
        $this->assertStringEndsWith('as fts_score', $result);
    }

    public function test_HasFullTextSearch_buildFullTextSearch_contains_MATCH(): void
    {
        $sql = TestHftsPost::buildFullTextSearch(['title', 'body'], 'hello');
        $this->assertStringContainsString('MATCH', $sql);
        $this->assertStringContainsString('AGAINST', $sql);
    }

    public function test_HasFullTextSearch_buildFullTextSearch_bool_mode(): void
    {
        $sql = TestHftsPost::buildFullTextSearch(['title'], 'test', true);
        $this->assertStringContainsString('IN BOOLEAN MODE', $sql);
    }

    public function test_HasFullTextSearch_selectFTSScore_ends_with_fts_score(): void
    {
        $result = TestHftsPost::selectFTSScore(['title'], 'test');
        $this->assertStringEndsWith('as fts_score', $result);
    }

    public function test_FullTextSearchTrait_has_orFullTextSearch_scope(): void
    {
        $this->assertTrue(method_exists(TestFtsPost::class, 'scopeOrFullTextSearch'));
    }

    public function test_HasFullTextSearch_does_not_have_orFullTextSearch_scope(): void
    {
        $this->assertFalse(method_exists(TestHftsPost::class, 'scopeOrFullTextSearch'));
    }
}
