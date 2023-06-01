<?php

namespace  Osoobe\Utilities\Helpers;


class LocationHelper {


    /**
     * Get country codes
     *
     * @see https://github.com/lukes/ISO-3166-Countries-with-Regional-Codes/blob/master/all/all.json
     * @link https://github.com/lukes/ISO-3166-Countries-with-Regional-Codes/blob/master/all/all.csv
     *
     * @param boolean $as_collection
     * @return \Illuminate\Support\Collection|array
     */
    public static function get_country_codes(bool $as_collection=true) {
        $path = resource_path("assets/country-codes.json");
        $data = AppHelper::loadJson($path);
        if ( $as_collection ) {
            return collect($data);
        }
        return $data;
    }

    /**
     * Country name match
     *
     * @param string $name
     * @param array $country
     * @return bool
     */
    public static function country_name_match(string $name, array $country) {
        return in_array(
            strtolower($name),
            [
                strtolower($country['name']),
                strtolower($country['alpha-2']),
                strtolower($country['alpha-3'])
            ]
        );
    }


    /**
     * Sanitize street address
     *
     * @param string $str
     * @param string $length
     * @return string
     */
    public static function santitizeAddress(string $str, string $format='abbr'): string {
        $abbrs = config('regex-patterns.location');
        foreach($abbrs as $pattern => $string) {
            if ( empty($string[$format]) ) {
                $format = 'abbr';
            }
            $str = trim(preg_replace( '/(^|\s)'. $pattern .'(\s|\.|$)/i', " ".$string[$format]." ", $str));
        }
        return $str;
    }


}


?>
