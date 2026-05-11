<?php

namespace Osoobe\Utilities\Tests\Traits;

use Carbon\Carbon;
use Osoobe\Utilities\Tests\Models\TestRecord;
use Osoobe\Utilities\Tests\TestCase;

class ModelDefaultTraitTest extends TestCase
{
    public function test_creating_without_status_sets_default_active(): void
    {
        $record = TestRecord::create(['name' => 'Test']);
        $this->assertSame('active', $record->status);
    }

    public function test_creating_with_explicit_status_keeps_provided(): void
    {
        $record = TestRecord::create(['name' => 'Test', 'status' => 'inactive']);
        $this->assertSame('inactive', $record->status);
    }

    public function test_creating_without_trial_ends_at_sets_14_days(): void
    {
        $record = TestRecord::create(['name' => 'Test']);
        $this->assertNotNull($record->trial_ends_at);
        $trialEndsAt = Carbon::parse($record->trial_ends_at);
        $diffDays = Carbon::now()->diffInDays($trialEndsAt, false);
        $this->assertGreaterThanOrEqual(13, $diffDays);
        $this->assertLessThanOrEqual(15, $diffDays);
    }

    public function test_creating_with_explicit_trial_ends_at_keeps_provided(): void
    {
        $customDate = Carbon::now()->addDays(30)->toDateTimeString();
        $record = TestRecord::create(['name' => 'Test', 'trial_ends_at' => $customDate]);
        $trialEndsAt = Carbon::parse($record->trial_ends_at);
        $diffDays = Carbon::now()->diffInDays($trialEndsAt, false);
        $this->assertGreaterThanOrEqual(28, $diffDays);
    }

    public function test_update_does_not_reset_status(): void
    {
        $record = TestRecord::create(['name' => 'Test', 'status' => 'inactive']);
        $record->name = 'Updated';
        $record->save();
        $record->refresh();
        $this->assertSame('inactive', $record->status);
    }
}
