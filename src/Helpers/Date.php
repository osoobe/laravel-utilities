<?php

namespace  Osoobe\Utilities\Helpers;

use Carbon\Carbon;

class Date {

    /**
     * Get the start and end date period for the given date
     *
     * @param Carbon $date          Default is tdoay.
     * @param string $period        Default is monthly.
     * @return object               Object that has the following fields:
     *  - start_date
     *  - end_date
     *  - date
     */
    public static function getStartEndDate(Carbon $date=null, $period="monthly") {
        if ( ! $date ) {
            $date = Carbon::now();
        }
        switch ($period) {
            case 'daily':
                $start_date = $date->copy()->startOfDay();
                $end_date = $date->copy()->endOfDay();
                break;
            case 'weekly':
                $start_date = $date->copy()->startOfWeek();
                $end_date = $date->copy()->endOfWeek();
                break;
            case 'monthly':
                $start_date = $date->copy()->startOfMonth();
                $end_date = $date->copy()->endOfMonth();
                break;
            default:
                $start_date = $date->copy()->startOfMonth();
                $end_date = $date->copy()->endOfMonth();
                break;
        }
        return (object) [
            'date' => $date,
            'start_date' => $start_date,
            'end_date' => $end_date
        ];
    }

}


?>
