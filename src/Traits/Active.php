<?php

namespace  Osoobe\Utilities\Traits;

trait Active
{

    /**
     * Scope a query to only include active objects.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', '=', 1);
    }

    /**
     * Scope a query to only include not active objects.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotActive($query)
    {
        return $query->where('is_active', '!=', 1);
    }


    /**
     * Scope a query to only include active objects.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHidden($query)
    {
        return $query->where('hidden', '=', 1);
    }

}
