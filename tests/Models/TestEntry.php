<?php

namespace Osoobe\Utilities\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Osoobe\Utilities\Traits\Userstamp;

class TestEntry extends Model
{
    use Userstamp;

    protected $table = 'test_entries';
    protected $guarded = [];
}
