<?php

namespace  Osoobe\Utilities\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;

trait HasSlug {


    public function hasSlug($query, $slug) {
        return $query->where('slug', $slug);
    }

    public static function findBySlugOrFail($slug) {
        $result = self::where('slug', $slug)
            ->orWhere('id', $slug)->first();
        if ( ! $result ) {
            throw (new ModelNotFoundException)->setModel(
                self::class, $slug
            );
            // return abort(404);
        }
        return $result;
    }


}
