<?php


namespace  Osoobe\Utilities\Traits;


trait AdvanceWhereQuery {


    /**
     * Scope a query to only include active objects.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereKeyOrNull($query, $key, $value) {
        if ( empty($value) ) {
            return $query;
        }
        return $query->where($key, $value);
    }

}

?>
