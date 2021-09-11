<?php

namespace  Osoobe\Utilities\Traits;

trait Sorted
{

    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order');
    }

}
