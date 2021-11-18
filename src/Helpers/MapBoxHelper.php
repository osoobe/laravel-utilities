<?php

namespace  Osoobe\Utilities\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MapBoxHelper {

    public static function queryLocationData(string $apikey, string $address, $limit=1, $params=[]) {

        $defaults = [
            "limit" => $limit,
            "language" => "en-US",
            "country" => "US"
        ];
        $params = array_merge($defaults, $params);
        $params['access_token'] = $apikey;
        $param_str = http_build_query($params);
        $query_string = "https://api.mapbox.com/geocoding/v5/mapbox.places/$address.json?$param_str";

        try {
            return Http::get($query_string)->json();
        } catch (\Throwable $th) {
            Log::warning($th->getMessage());
            return False;
        }
    }

    public static function getCordsFromData($mapbox_data) {
        if ( empty($mapbox_data) ) {
            return null;
        }
        try {
            $cords = $mapbox_data['features'][0]['geometry']['coordinates'];
            return (object) [
                "latitude" => $cords[1],
                'longitude' => $cords[0]
            ];
        } catch (\Throwable $th) {
            return null;
        }
    }

}



?>
