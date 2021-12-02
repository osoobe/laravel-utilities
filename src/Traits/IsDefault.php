<?php

namespace  Osoobe\Utilities\Traits;

trait IsDefault {

    /**
     * Scope a query to only include default objects.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsDefault($query)
    {
        return $query->where('is_default', '=', 1);
    }

    /**
     * Scope a query to only include not default objects.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotDefault($query)
    {
        return $query->where('is_default', '!=', 1);
    }

}
