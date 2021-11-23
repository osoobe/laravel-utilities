<?php

namespace  Osoobe\Utilities\Traits;

use Carbon\Carbon;


/**
 * Use time difference between the current date and the given date files.
 * @property-read string $posted_time_diff          Time difference between the current date
 *                                                  and the posted date.
 * @property-read string $expiry_date_time_diff     Time difference between the current date
 *                                                  and the expiry date.
 */
trait TimeDiff {

    /**
     * Get the time difference between the current date and
     * the created date.
     *
     * @uses Carbon\Carbon::diffForHumans
     * @example $this->posted_time_diff  1 day ago.
     * @return string
     */
    public function getCreatedTimeDiffAttribute() {
        try {
            return $this->created_at->diffForHumans(
                ['options' => Carbon::JUST_NOW]
            );
        } catch (\Throwable $th) {
            return "Non-Disclosure ";
        }
    }

    public function formatDateField($field) {
        if ( empty($this->$field) ) {
            return '';
        }
        return $this->$field->format('Y-m-d');
    }


    /**
     * Get the time difference between the current date and
     * the created date.
     *
     * @uses Carbon\Carbon::diffForHumans
     * @example $this->posted_time_diff  1 day ago.
     * @return string
     */
    public function getPostedTimeDiffAttribute() {
        return $this->created_time_diff;
    }


    /**
     * Get the time difference between the current date and
     * the expiry date.
     *
     * @uses Carbon\Carbon::diffForHumans
     * @example $this->posted_time_diff  1 day ago.
     * @return string
     */
    public function getExpiryDateTimeDiffAttribute() {
        if ( !isset($this->expiry_date)) {
            return "Non-Disclosure ";
        }
        return $this->expiry_date->diffForHumans(
            ['options' => Carbon::JUST_NOW]
        );
    }

    /**
     * Check if the model is expired.
     *
     * @return boolean
     */
    public function isExpired(){
        if ( !isset($this->expiry_date)) {
            return "Non-Disclosure ";
        }
        return $this->expiry_date < Carbon::now();
    }

    /**
     * Set expiry_date by the number of days from the current date.
     *
     * @param integer $days
     * @return void
     */
    public function expireInDays(int $days){
        if ( !isset($this->expiry_date)) {
            return "Non-Disclosure ";
        }
        $this->expiry_date = Carbon::now()->addDays($days);
    }

    /**
     * Set expiry_date by the number of hours from the current datetime.
     *
     * @param integer $days
     * @return void
     */
    public function expireInHours(int $hours) {
        if ( !isset($this->expiry_date)) {
            return "Non-Disclosure ";
        }
        $this->expiry_date = Carbon::now()->addHours($hours);
    }


    /**
     * Scope a query to only exclude expired objects.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query) {
        if ( !isset($this->expiry_date)) {
            return $query;
        }
        return $query->whereDate('expiry_date', '<',  Carbon::now());
    }


    /**
     * Scope a query to only include not expired objects.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotExpired($query) {
        if ( !isset($this->expiry_date)) {
            return $query;
        }
        return $query->whereDate('expiry_date', '>=',  Carbon::now());
    }


    /**
     * Scope a query for objects that were created within the last 7 days.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedSinceWeek($query) {
        return $query->whereDate('created_at', '>=',  Carbon::now()->subDays(7));
    }


    /**
     * Scope a query for objects that were created within the last given days.
     * Default is 3 days.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param int $days     Within the last given days
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecentlyCreated($query, int $time=3, string $carbon_fn = 'subDays') {
        try {
            return $query->whereDate('created_at', '>=',  Carbon::now()->$carbon_fn($time));
        } catch (\Throwable $th) {
            return $query->whereDate('created_at', '>=', Carbon::now()->subDays($time));
        }
    }

    /**
     * Scope a query for objects that were created within the last given days.
     * Default is 3 days.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param int $days     Within the last given days
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecentlyUpdated($query, int $time=3, string $carbon_fn = 'subDays') {
        try {
            return $query->whereDate('updated_at', '>=',  Carbon::now()->$carbon_fn($time));
        } catch (\Throwable $th) {
            return $query->whereDate('updated_at', '>=', Carbon::now()->subDays($time));
        }
    }


    /**
     * Scope a query for objects that were created today.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedToday($query) {
        return $query->whereDate('created_at', '>=',  Carbon::now()->startOfDay() );
    }


    /**
     * Check if the model was recently created.
     * Default is 1 day.
     *
     * @param int $time             Within the last given days, hours, minutes, etc.
     * @param string $carbon_fn     Carbon subtract function
     * @return bool
     */
    public function recentlyCreated(int $time=1, string $carbon_fn = 'subDays') {
        try {
            return $this->created_at >=  Carbon::now()->$carbon_fn($time);
        } catch (\Throwable $th) {
            return $this->created_at >=  Carbon::now()->subDays($time);
        }
    }


    /**
     * Check if the model was recently updated.
     * Default is 1 hour.
     *
     * @param int $time             Within the last given days, hours, minutes, etc.
     * @param string $carbon_fn     Carbon subtract function
     * @return bool
     */
    public function recentlyUpdated(int $time=1, string $carbon_fn = 'subHours') {
        try {
            return $this->updated_at >=  Carbon::now()->$carbon_fn($time);
        } catch (\Throwable $th) {
            return $this->updated_at >=  Carbon::now()->subDays($time);
        }
    }


    /**
     * Scope a query for objects that were updated within the last 7 days.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpdatedSinceWeek($query) {
        return $query->whereDate('updated_at', '>=',  Carbon::now()->subDays(7));
    }

    /**
     * Scope a query for objects that were created within the last given hours.
     * Default is 3.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param int $hours     Within the last given hours
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedHoursAgo($query, $hours=3) {
        return $query->whereDate('created_at', '>=',  Carbon::now()->subHours($hours));
    }

    /**
     * Scope a query for objects that were created within the last given minutes.
     * Default is 3.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param int $mins     Within the last given minutes
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedMinutesAgo($query, $mins=3) {
        return $query->whereDate('created_at', '>=',  Carbon::now()->subMinutes($mins));
    }

}

?>
