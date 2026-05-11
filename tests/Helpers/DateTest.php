<?php

namespace Osoobe\Utilities\Tests\Helpers;

use Carbon\Carbon;
use Osoobe\Utilities\Helpers\Date;
use Osoobe\Utilities\Tests\TestCase;

class DateTest extends TestCase
{
    public function test_getStartEndDate_null_monthly_start_and_end_of_month(): void
    {
        $result = Date::getStartEndDate(null, 'monthly');
        $this->assertInstanceOf(Carbon::class, $result->start_date);
        $this->assertInstanceOf(Carbon::class, $result->end_date);
        $this->assertSame(1, (int) $result->start_date->format('d'));
        $this->assertSame((int) Carbon::now()->endOfMonth()->format('d'), (int) $result->end_date->format('d'));
    }

    public function test_getStartEndDate_weekly_start_and_end_of_week(): void
    {
        $date = Carbon::now();
        $result = Date::getStartEndDate($date, 'weekly');
        $this->assertInstanceOf(Carbon::class, $result->start_date);
        $this->assertInstanceOf(Carbon::class, $result->end_date);
        // start of week is Monday (or Sunday depending on locale), end is Sunday (or Saturday)
        $this->assertTrue($result->start_date <= $result->end_date);
    }

    public function test_getStartEndDate_daily(): void
    {
        $date = Carbon::parse('2024-06-15');
        $result = Date::getStartEndDate($date, 'daily');
        $this->assertSame('2024-06-15 00:00:00', $result->start_date->format('Y-m-d H:i:s'));
        $this->assertSame('2024-06-15 23:59:59', $result->end_date->format('Y-m-d H:i:s'));
    }

    public function test_getStartEndDate_day_alias(): void
    {
        $date = Carbon::parse('2024-06-15');
        $result = Date::getStartEndDate($date, 'day');
        $this->assertSame('2024-06-15 00:00:00', $result->start_date->format('Y-m-d H:i:s'));
    }

    public function test_getStartEndDate_week_alias(): void
    {
        $date = Carbon::now();
        $result = Date::getStartEndDate($date, 'week');
        $this->assertTrue($result->start_date <= $result->end_date);
    }

    public function test_getStartEndDate_month_alias(): void
    {
        $date = Carbon::parse('2024-06-15');
        $result = Date::getStartEndDate($date, 'month');
        $this->assertSame(1, (int) $result->start_date->format('d'));
        $this->assertSame(30, (int) $result->end_date->format('d'));
    }

    public function test_getStartEndDate_unknown_defaults_to_monthly(): void
    {
        $date = Carbon::parse('2024-06-15');
        $result = Date::getStartEndDate($date, 'unknown');
        $this->assertSame(1, (int) $result->start_date->format('d'));
        $this->assertSame(30, (int) $result->end_date->format('d'));
    }

    public function test_isBetweenPeriod_today_in_current_month_returns_true(): void
    {
        $this->assertTrue(Date::isBetweenPeriod(Carbon::now(), 'monthly'));
    }

    public function test_isBetweenPeriod_old_date_returns_true_because_period_is_relative_to_date(): void
    {
        // isBetweenPeriod computes start/end relative to the given date,
        // so the date is always within its own period — this is the actual behavior.
        $this->assertTrue(Date::isBetweenPeriod(Carbon::now()->subMonths(2), 'monthly'));
    }
}
