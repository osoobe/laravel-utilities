<?php

namespace Osoobe\Utilities\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Osoobe\Utilities\Traits\SEO;

class TestPage extends Model
{
    use SEO;

    protected $table = 'test_posts';
    protected $guarded = [];

    public function getRouteURL(): string
    {
        return '/pages/' . $this->slug;
    }

    public function getSEOTitleAttribute(): string
    {
        return $this->title . ' | Site';
    }

    public function getSEODescriptionAttribute(): string
    {
        return 'Description: ' . $this->title;
    }
}
