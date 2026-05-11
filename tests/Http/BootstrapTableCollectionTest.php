<?php

namespace Osoobe\Utilities\Tests\Http;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Osoobe\Utilities\Http\Resources\BootstrapTableCollection;
use Osoobe\Utilities\Tests\TestCase;

class BootstrapTableCollectionTest extends TestCase
{
    public function test_wraps_plain_collection_with_rows_and_total(): void
    {
        $collection = collect([
            ['id' => 1, 'name' => 'Alice'],
            ['id' => 2, 'name' => 'Bob'],
        ]);

        $resource = new BootstrapTableCollection($collection);
        $request = Request::create('/', 'GET');
        $array = $resource->toArray($request);

        $this->assertArrayHasKey('rows', $array);
        $this->assertArrayHasKey('total', $array);
        $this->assertSame(2, $array['total']);
    }

    public function test_wraps_paginator_and_uses_paginator_total(): void
    {
        $items = collect([
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
        ]);

        $paginator = new LengthAwarePaginator($items->take(2), 3, 2, 1);
        $resource = new BootstrapTableCollection($paginator);
        $request = Request::create('/', 'GET');
        $array = $resource->toArray($request);

        $this->assertArrayHasKey('rows', $array);
        $this->assertArrayHasKey('total', $array);
        $this->assertSame(3, $array['total']);
    }

    public function test_wrap_is_null(): void
    {
        $this->assertNull(BootstrapTableCollection::$wrap);
    }
}
