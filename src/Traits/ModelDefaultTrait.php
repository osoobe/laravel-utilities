<?php

namespace  Osoobe\Utilities\Traits;

use DB;
use Illuminate\Support\Facades\Log;

trait ModelDefaultTrait {

    public abstract function defaultModelValues(): void;


    /**
     * Create Notification Settings.
     *
     * @todo create notification setting package.
     * @return void
     */
    protected static function bootModelDefaultTrait(): void {
        static::creating(function ($model) {
            try {
                return $model->defaultModelValues();
            } catch (\Throwable $th) {
                Log::error($th->getMessage());
            }
        });
    }

}
