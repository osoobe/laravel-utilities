<?php

namespace Osoobe\Utilities\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Osoobe\Utilities\Traits\HasFullTextSearch;

class TestHftsPost extends Model
{
    use HasFullTextSearch;

    protected $table = 'test_fts_posts';
    protected $guarded = [];
}
