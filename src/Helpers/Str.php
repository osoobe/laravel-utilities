<?php

namespace  Osoobe\Utilities\Helpers;

use Illuminate\Support\Str as ParentStr;

class Str extends ParentStr {

    public static function ucwords($text) {
        return ucwords(strtolower($text));
    }


    public static function nameParts(string $full_name) {
        $splitName = explode(' ', $full_name); // Restricts it to only 2 values, for names like Billy Bob Jones
        $first_name = $splitName[0];
        $last_name = $splitName[ count($splitName) - 1];

        $first_name = array_shift($splitName);
        $last_name = array_pop($splitName);
        $middle_name = trim(implode(' ', $splitName));
        return array_to_object([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'middle_name' => $middle_name
        ]);
    }

        /**
     * Convert a string to snake case.
     *
     * @param  string  $value
     * @param  string  $delimiter
     * @return string
     */
    public static function ucsnake(string $value, string $delimiter='_')
    {
        return static::ucwords(static::snake($value, $delimiter));
    }

    /**
     * Boolean to string
     *
     * @param boolean $bool
     * @param string $format    Options:
     *                          B -> 'True" or "False'
     *                          Y -> 'Yes" or "No'
     * @return bool
     */
    public static function boolToString(bool $bool, $format='Y') {
        if ( $format == 'B' ) {
            if ( $bool ) {
                return 'True';
            }
            return "False";
        }
        if ( $bool ) {
            return 'Yes';
        }
        return "No";
    }

}

?>
