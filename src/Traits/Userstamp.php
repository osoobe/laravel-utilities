<?php

namespace App\Traits;

use App\Helpers\AppHelper;
use App\NotificationSetting;
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
            $user = AppHelper::get_auth_user();
            if ( $user ) {
                $model->creator_id = $user->id;
                $model->creator_type = get_class($user);
                $model->editor_id = $user->id;
                $model->editor_type = get_class($user);
            }
        });
        static::updating(function ($model) {
            $user = AppHelper::get_auth_user();
            if ( $user ) {
                $model->editor_id = $user->id;
                $model->editor_type = get_class($user);
            }
        });
    }

}

?>