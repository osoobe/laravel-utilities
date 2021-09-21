<?php

namespace  Osoobe\Utilities\Helpers;

class GoogleMapsHelper {

    public static function getGoogleMapCordinates($apikey, $address, $region="US", $sensor="false") {
        $url = "http://maps.google.com/maps/api/geocode/json?address=$address&sensor=$sensor&region=$region";
        $response = file_get_contents($url);
        $response = json_decode($response, true);

        $lat = $response['results'][0]['geometry']['location']['lat'];
        $long = $response['results'][0]['geometry']['location']['lng'];
        return [
            "latitude" => $lat,
            "longitude" => $long
        ];
    }


}



?>
