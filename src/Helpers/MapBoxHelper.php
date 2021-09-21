<?php

namespace  Osoobe\Utilities\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MapBoxHelper {

    public static function queryLocationData(string $apikey, string $address, $limit=1) {
        $query_string = "https://api.mapbox.com/geocoding/v5/mapbox.places/$address.json"
            ."?limit=$limit&language=en-US&access_token=$apikey";

        try {
            return Http::get($query_string)->json();
        } catch (\Throwable $th) {
            Log::warning($th->getMessage());
            return False;
        }
    }

}



?>
