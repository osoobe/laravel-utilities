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
            'addLocationIndex', function () {
                $this->index(['country']);
                $this->index(['state']);
                $this->index(['city']);
                $this->index(['street_address']);
                $this->index(['zip_code']);
            }
        );
        // Active status
        Blueprint::macro(
            'dropLocationIndex', function () {
                $this->dropIndex(['country']);
                $this->dropIndex(['state']);
                $this->dropIndex(['city']);
                $this->dropIndex(['street_address']);
                $this->dropIndex(['zip_code']);
            }
        );

        // Active status
        Blueprint::macro(
            'location', function () {
                $this->string('country', 50)->nullable();
                $this->string('state', 50)->nullable();
                $this->string('city', 50)->nullable();
                $this->string('street_address', 250)->nullable();
                $this->string('zip_code', 15)->nullable();
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
            'coordinates', function () {
                $this->decimal('latitude', 10, 8)->nullable();
                $this->decimal('longitude', 11, 8)->nullable();
            }
        );
        Blueprint::macro(
            'dropCoordinates', function () {
                $this->dropColumn('latitude');
                $this->dropColumn('longitude');
            }
        );

        // Manage by blueprint
        Blueprint::macro(
            'userstamp', function () {
                $this->nullableMorphs('creator');
                $this->nullableMorphs('editor');
            }
        );
        Blueprint::macro(
            'dropUserstamp', function () {
                $this->dropColumn('creator_id');
                $this->dropColumn('creator_type');
                $this->dropColumn('editor_id');
                $this->dropColumn('editor_type');
            }
        );

        // Manage by blueprint
        Blueprint::macro(
            'userstamp', function () {
                $this->nullableMorphs('creator');
                $this->nullableMorphs('editor');
            }
        );
        Blueprint::macro(
            'dropUserstamp', function () {
                $this->dropColumn('creator_id');
                $this->dropColumn('creator_type');
                $this->dropColumn('editor_id');
                $this->dropColumn('editor_type');
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
            return Helpers\PhoneNumberHelper::isValid($value);
        });

        \Illuminate\Support\Facades\Validator::replacer('phone', function($message, $attribute, $rule, $parameters) {
                return str_replace(':attribute',$attribute, ':attribute is invalid phone number');
            });


        \Illuminate\Support\Facades\Validator::extend('password', function($attribute, $value, $parameters, $validator) {
            return (bool) preg_match('%'. config('validation.password.pattern') .'%', $value);
        });

        \Illuminate\Support\Facades\Validator::replacer('password', function($message, $attribute, $rule, $parameters) {
                return str_replace(':attribute',$attribute,
                    config(
                        'validation.password.message'));
            });

    }
}
