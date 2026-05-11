<?php

namespace Osoobe\Utilities\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class ServiceProviderTest extends TestCase
{
    // Blueprint Macros

    public function test_location_macro_adds_address_columns(): void
    {
        Schema::create('test_location_table', function (Blueprint $table) {
            $table->id();
            $table->location();
        });

        $this->assertTrue(Schema::hasColumn('test_location_table', 'country'));
        $this->assertTrue(Schema::hasColumn('test_location_table', 'state'));
        $this->assertTrue(Schema::hasColumn('test_location_table', 'city'));
        $this->assertTrue(Schema::hasColumn('test_location_table', 'street_address'));
        $this->assertTrue(Schema::hasColumn('test_location_table', 'zip_code'));

        Schema::dropIfExists('test_location_table');
    }

    public function test_coordinates_macro_adds_lat_lng_columns(): void
    {
        Schema::create('test_coords_table', function (Blueprint $table) {
            $table->id();
            $table->coordinates();
        });

        $this->assertTrue(Schema::hasColumn('test_coords_table', 'latitude'));
        $this->assertTrue(Schema::hasColumn('test_coords_table', 'longitude'));

        Schema::dropIfExists('test_coords_table');
    }

    public function test_userstamp_macro_adds_morph_columns(): void
    {
        Schema::create('test_stamp_table', function (Blueprint $table) {
            $table->id();
            $table->userstamp();
        });

        $this->assertTrue(Schema::hasColumn('test_stamp_table', 'creator_id'));
        $this->assertTrue(Schema::hasColumn('test_stamp_table', 'creator_type'));
        $this->assertTrue(Schema::hasColumn('test_stamp_table', 'editor_id'));
        $this->assertTrue(Schema::hasColumn('test_stamp_table', 'editor_type'));

        Schema::dropIfExists('test_stamp_table');
    }

    public function test_isActive_macro_adds_is_active_column(): void
    {
        Schema::create('test_active_table', function (Blueprint $table) {
            $table->id();
            $table->isActive();
        });

        $this->assertTrue(Schema::hasColumn('test_active_table', 'is_active'));

        Schema::dropIfExists('test_active_table');
    }

    public function test_dropLocation_macro_is_registered(): void
    {
        // Verify the dropLocation macro is registered by checking the Blueprint has it
        $this->assertTrue(\Illuminate\Database\Schema\Blueprint::hasMacro('dropLocation'));
    }

    public function test_dropCoordinates_macro_is_registered(): void
    {
        $this->assertTrue(\Illuminate\Database\Schema\Blueprint::hasMacro('dropCoordinates'));
    }

    public function test_dropUserstamp_macro_is_registered(): void
    {
        $this->assertTrue(\Illuminate\Database\Schema\Blueprint::hasMacro('dropUserstamp'));
    }

    public function test_dropIsActive_macro_is_registered(): void
    {
        $this->assertTrue(\Illuminate\Database\Schema\Blueprint::hasMacro('dropIsActive'));
    }

    // Custom Validators

    public function test_phone_validator_passes_for_valid_phone(): void
    {
        $validator = Validator::make(['p' => '+15551234567'], ['p' => 'phone']);
        $this->assertTrue($validator->passes());
    }

    public function test_phone_validator_fails_for_invalid_input(): void
    {
        $validator = Validator::make(['p' => 'abc'], ['p' => 'phone']);
        $this->assertFalse($validator->passes());
    }

    public function test_password_pattern_matches_valid_password(): void
    {
        // Laravel 8 has a built-in Password rule that takes precedence over Validator::extend('password').
        // Test the regex pattern directly as the service provider registers it.
        $pattern = config('validation.password.pattern');
        $this->assertNotEmpty($pattern);
        // Pattern: ^(?=.*[A-Z])(?=.*\d).{8,}$ - at least 1 uppercase, 1 digit, 8 chars
        $this->assertSame(1, preg_match('%' . $pattern . '%', 'Password1'));
    }

    public function test_password_pattern_rejects_weak_password(): void
    {
        $pattern = config('validation.password.pattern');
        $this->assertNotEmpty($pattern);
        $this->assertSame(0, preg_match('%' . $pattern . '%', 'weakpassword'));
    }

    // Route

    public function test_ajax_resource_route_is_registered(): void
    {
        $routes = Route::getRoutes();
        $found = false;
        foreach ($routes as $route) {
            if ($route->uri() === 'api/resources/{slug}/{format}') {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Route api/resources/{slug}/{format} is not registered');
    }

    public function test_ajax_resource_route_is_named_correctly(): void
    {
        $route = Route::getRoutes()->getByName('api.resource.get');
        $this->assertNotNull($route);
    }

    public function test_hitting_nonexistent_slug_returns_empty_result(): void
    {
        $response = $this->getJson('/api/resources/nonexistent/bst');
        $response->assertStatus(200);
        $response->assertJson(['result' => []]);
    }
}
