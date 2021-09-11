<?php

namespace Osoobe\Utilities\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BootstrapTableCollection extends ResourceCollection
{

    public static $wrap = null;

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
        $data["total"] = count($data["rows"]);
        return $data;
    }
}
