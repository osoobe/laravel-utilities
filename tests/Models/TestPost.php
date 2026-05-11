<?php

namespace Osoobe\Utilities\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Osoobe\Utilities\Traits\Active;
use Osoobe\Utilities\Traits\AdvanceWhereQuery;
use Osoobe\Utilities\Traits\HasEmail;
use Osoobe\Utilities\Traits\HasSlug;
use Osoobe\Utilities\Traits\HasVerified;
use Osoobe\Utilities\Traits\IsDefault;
use Osoobe\Utilities\Traits\Sorted;
use Osoobe\Utilities\Traits\TimeDiff;

class TestPost extends Model
{
    use Active, AdvanceWhereQuery, HasEmail, HasSlug, HasVerified, IsDefault, Sorted, TimeDiff;

    protected $table = 'test_posts';
    protected $guarded = [];
    protected $casts = ['expiry_date' => 'datetime', 'email_verified_at' => 'datetime'];
}
