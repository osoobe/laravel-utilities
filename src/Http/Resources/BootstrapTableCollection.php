<?php

namespace Osoobe\Utilities\Http\Resources;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\AbstractPaginator;

class BootstrapTableCollection extends ResourceCollection
{

    public static $wrap = null;

    protected $paginator = null;

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @return void
     */
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->resource = $this->collectResource($resource);


        try {
            if ( $resource->hasPages() ) {
                $this->paginator = $resource;
            }
        } catch (\Throwable $th) {
            if (
                $resource instanceof \Illuminate\Pagination\Paginator ||
                $resource instanceof \Illuminate\Pagination\LengthAwarePaginator

            ){
                $this->paginator = $resource;
            }
        }
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            "rows" => $this->collection
        ];
        $data["total"] = ( !empty($this->paginator) )? $this->paginator->total() :  count($data["rows"]);
        return $data;
    }



    /**
     * Create a new anonymous resource collection.
     *
     * @param  mixed  $resource
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public static function collection($resource)
    {
        return tap(new AnonymousResourceCollection($resource, static::class), function ($collection) {
            if (property_exists(static::class, 'preserveKeys')) {
                $collection->preserveKeys = (new static([]))->preserveKeys === true;
            }
        });
    }
}
