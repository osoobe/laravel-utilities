<?php

namespace Osoobe\Utilities\Tests\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class TestUser extends Authenticatable
{
    protected $table = 'test_users';
    protected $guarded = [];
    protected $hidden = ['password', 'remember_token'];
}
