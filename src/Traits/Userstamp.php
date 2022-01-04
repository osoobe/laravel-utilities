<?php

namespace  Osoobe\Utilities\Traits;

use Illuminate\Support\Facades\Auth;

trait Userstamp {

    /**
     * Create Notification Settings.
     *
     * @todo create notification setting package.
     * @return void
     */
    protected static function bootUserstamp(): void {
        static::creating(function ($model) {
            $user = Auth::user();
            if ( $user ) {
                $model->creator_id = $user->id;
                $model->creator_type = get_class($user);
                $model->editor_id = $user->id;
                $model->editor_type = get_class($user);
            }
        });
        static::updating(function ($model) {
            $user = Auth::user();
            if ( $user ) {
                $model->editor_id = $user->id;
                $model->editor_type = get_class($user);
            }
        });
    }

}

?>
