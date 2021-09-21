<?php

namespace  Osoobe\Utilities\Traits;

trait HasEmail {
     /**
     * Scope a query to only include active objects.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEmail($query, $email)
    {
        return $query->where('email', $email);
    }
}
