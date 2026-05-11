<?php

namespace Osoobe\Utilities\Tests\Http;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Osoobe\Utilities\Http\Resources\Select2Collection;
use Osoobe\Utilities\Http\Resources\Select2Resource;
use Osoobe\Utilities\Tests\Models\TestPost;
use Osoobe\Utilities\Tests\TestCase;

class Select2ResourceTest extends TestCase
{
    public function test_Select2Collection_wrap_is_results(): void
    {
        $this->assertSame('results', Select2Collection::$wrap);
    }

    public function test_Select2Resource_toArray_with_model_configs(): void
    {
        $post = TestPost::create(['title' => 'My Post', 'slug' => 'my-post']);

        $request = Request::create('/', 'GET');
        $request->merge([
            'model_configs' => [
                'id_column' => 'id',
                'text_column' => 'title',
                'includes' => ['title', 'slug'],
            ],
        ]);

        $resource = new Select2Resource($post);
        $array = $resource->toArray($request);

        $this->assertSame($post->id, $array['id']);
        $this->assertSame('My Post', $array['text']);
    }

    public function test_Select2Resource_toArray_without_model_configs_auto_detects_title(): void
    {
        $post = TestPost::create(['title' => 'Auto Post']);

        $request = Request::create('/', 'GET');

        $resource = new Select2Resource($post);
        $array = $resource->toArray($request);

        $this->assertSame('Auto Post', $array['text']);
        $this->assertSame((int) $post->id, $array['model_id']);
    }

    public function test_Select2Resource_toArray_sets_model_id(): void
    {
        $post = TestPost::create(['title' => 'Post with ID']);

        $request = Request::create('/', 'GET');
        $resource = new Select2Resource($post);
        $array = $resource->toArray($request);

        $this->assertArrayHasKey('model_id', $array);
        $this->assertSame((int) $post->id, $array['model_id']);
    }
}
