<?php

namespace  Osoobe\Utilities\Traits;

use Carbon\Carbon;

trait HasEmail
{

    /**
     * Check if email is verified
     *
     * @return bool
     */
    public function isEmailVerified() {
        return !empty($this->email) && !empty($this->email_verified_at);
    }

    /**
     * Scope a query to only include objects  verified by email.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param string $email
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEmail($query, string $email)
    {
        return $query->where('email', $email);
    }


    /**
     * Scope a query to only include objects verified by email.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEmailVerified($query)
    {
        return $query->where('email_verified_at', '!=', null);
    }


    /**
     * Scope a query to only include objects with email verified.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEmailNotVerified($query)
    {
        return $query->where('email_verified_at', null);
    }


    /**
     * Scope a query for objects that were verified by email within the last given days.
     * Default is 3 days.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param int $days     Within the last given days
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEmailVerifiedSince($query, int $time = 3, string $carbon_fn = 'subDays')
    {
        try {
            return $query->whereDate('created_at', '>=',  Carbon::now()->$carbon_fn($time));
        } catch (\Throwable $th) {
            return $query->whereDate('created_at', '>=', Carbon::now()->subDays($time));
        }
    }
}
