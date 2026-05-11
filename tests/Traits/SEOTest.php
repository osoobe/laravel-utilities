<?php

namespace Osoobe\Utilities\Tests\Traits;

use Osoobe\Utilities\Tests\Models\TestPage;
use Osoobe\Utilities\Tests\TestCase;

class SEOTest extends TestCase
{
    public function test_url_attribute_calls_getRouteURL(): void
    {
        $page = TestPage::create(['title' => 'My Page', 'slug' => 'my-page']);
        $this->assertSame('/pages/my-page', $page->url);
    }

    public function test_seo_title_returns_formatted_title(): void
    {
        $page = TestPage::create(['title' => 'My Page', 'slug' => 'my-page']);
        $this->assertSame('My Page | Site', $page->seo_title);
    }

    public function test_seo_description_returns_formatted_description(): void
    {
        $page = TestPage::create(['title' => 'My Page', 'slug' => 'my-page']);
        $this->assertSame('Description: My Page', $page->seo_description);
    }
}
