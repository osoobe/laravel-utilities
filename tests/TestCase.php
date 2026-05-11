<?php

namespace Osoobe\Utilities\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [\Osoobe\Utilities\UtilitiesServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]);
        $app['config']->set('validation.password.pattern', '^(?=.*[A-Z])(?=.*\d).{8,}$');
        $app['config']->set('validation.password.message', 'The :attribute must contain at least one uppercase letter and one number.');
        $app['config']->set('validation.phone.pattern', '%^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\./0-9]*$%i');
        $app['config']->set('auth.guards.web.driver', 'session');
        $app['config']->set('auth.guards.web.provider', 'users');
        $app['config']->set('auth.providers.users.driver', 'eloquent');
        $app['config']->set('auth.providers.users.model', \Osoobe\Utilities\Tests\Models\TestUser::class);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTables();
    }

    protected function tearDown(): void
    {
        $this->dropTables();
        parent::tearDown();
    }

    protected function createTables(): void
    {
        Schema::create('test_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->string('slug')->nullable();
            $table->tinyInteger('is_active')->nullable()->default(1);
            $table->tinyInteger('hidden')->nullable()->default(0);
            $table->tinyInteger('verified')->nullable()->default(0);
            $table->tinyInteger('is_default')->nullable()->default(0);
            $table->integer('sort_order')->nullable()->default(0);
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('expiry_date')->nullable();
            $table->string('lang')->nullable();
            $table->timestamps();
        });

        Schema::create('test_entries', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->nullableMorphs('creator');
            $table->nullableMorphs('editor');
            $table->timestamps();
        });

        Schema::create('test_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('test_records', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('status')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();
        });

        Schema::create('test_fts_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->timestamps();
        });
    }

    protected function dropTables(): void
    {
        Schema::dropIfExists('test_posts');
        Schema::dropIfExists('test_entries');
        Schema::dropIfExists('test_users');
        Schema::dropIfExists('test_records');
        Schema::dropIfExists('test_fts_posts');
    }
}
