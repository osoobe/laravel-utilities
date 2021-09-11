<?php

namespace Osoobe\Utilities\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class Select2Collection extends ResourceCollection
{
    public static $wrap = 'results';

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection;
    }
}
