<?php

namespace Osoobe\Utilities\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Osoobe\Utilities\Traits\FullTextSearchTrait;

class TestFtsPost extends Model
{
    use FullTextSearchTrait;

    protected $table = 'test_fts_posts';
    protected $guarded = [];
}
