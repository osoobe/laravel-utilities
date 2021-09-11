<?php

namespace Osoobe\Utilities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class UtilitiesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Active status
        Blueprint::macro(
            'location', function () {
                $this->string('country')->nullable();
                $this->string('state')->nullable();
                $this->string('city')->nullable();
                $this->string('street_address')->nullable();
                $this->string('zip_code')->nullable();
            }
        );
        Blueprint::macro(
            'dropLocation', function () {
                $this->dropColumn('country');
                $this->dropColumn('state');
                $this->dropColumn('city');
                $this->dropColumn('street_address');
                $this->dropColumn('zip_code');
            }
        );


        // Is Active
        Blueprint::macro(
            'isActive', function () {
                $this->tinyInteger('is_active')->nullable()->default(1);
            }
        );
        Blueprint::macro(
            'dropIsActive', function () {
                $this->dropColumn('is_active');
            }
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
