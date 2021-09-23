<?php

namespace  Osoobe\Utilities\Traits;

trait HasVerified
{

    /**
     * Scope a query to only include active objects.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVerified($query)
    {
        return $query->where('verified', '=', 1);
    }

    /**
     * Scope a query to only include not active objects.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotVerified($query)
    {
        return $query->where('verified', '!=', 1);
    }

}
