<?php

namespace Osoobe\Utilities\Tests\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Osoobe\Utilities\Traits\ModelDefaultTrait;

class TestRecord extends Model
{
    use ModelDefaultTrait;

    protected $table = 'test_records';
    protected $guarded = [];

    public function defaultModelValues(): void
    {
        $this->status = $this->status ?? 'active';
        $this->trial_ends_at = $this->trial_ends_at ?? Carbon::now()->addDays(14);
    }
}
