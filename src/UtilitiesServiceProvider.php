<?php

namespace Osoobe\Utilities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;
use Osoobe\Utilities\Helpers\MigrationHelper;

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

        Blueprint::macro(
            'cordinates', function () {
                $this->decimal('latitude', 10, 8)->nullable();
                $this->decimal('longitude', 11, 8)->nullable();
            }
        );
        Blueprint::macro(
            'dropCordinates', function () {
                $this->dropColumn('latitude');
                $this->dropColumn('longitude');
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
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        \Illuminate\Support\Facades\Validator::extend('phone', function($attribute, $value, $parameters, $validator) {
            return preg_match('%^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\./0-9]*$%i', $value) && strlen($value) >= 10;
        });

        \Illuminate\Support\Facades\Validator::replacer('phone', function($message, $attribute, $rule, $parameters) {
                return str_replace(':attribute',$attribute, ':attribute is invalid phone number');
            });
    }
}
