<?php

namespace  Osoobe\Utilities\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MapBoxHelper
{

    public static function queryLocationData(string $apikey, string $address, $limit = 1, $params = [])
    {

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



    public static function queryCoordinates(string $apikey, $latitude, $longitude, $limit = 1, $params = [])
    {

        $defaults = [
            "limit" => $limit,
            "language" => "en-US",
            "country" => "US"
        ];
        $params = array_merge($defaults, $params);
        $params['access_token'] = $apikey;
        $param_str = http_build_query($params);
        $query_string = "https://api.mapbox.com/geocoding/v5/mapbox.places/$longitude,$latitude.json?$param_str";

        try {
            return Http::get($query_string)->json();
        } catch (\Throwable $th) {
            Log::warning($th->getMessage());
            return False;
        }
    }

    public static function getCordsFromData($mapbox_data, $is_point = true)
    {
        if (empty($mapbox_data)) {
            return null;
        }
        try {
            $cords = $mapbox_data['features'][0]['geometry']['coordinates'];
            if (!$is_point && count($mapbox_data['features'][0]['bbox']) >= 4) {
                $cords = [
                    ($mapbox_data['features'][0]['bbox'][0] + $mapbox_data['features'][0]['bbox'][2]) / 2,
                    ($mapbox_data['features'][0]['bbox'][1] + $mapbox_data['features'][0]['bbox'][3]) / 2,
                ];
            }
            return (object) [
                "latitude" => $cords[1],
                'longitude' => $cords[0]
            ];
        } catch (\Throwable $th) {
            return null;
        }
    }

    /**
     * Get address component from mapbox data
     *
     * @param array|null $mapbox_data
     * @return object
     */
    public static function getAddressComponent(?array $mapbox_data): object
    {

        // Extract the address components from the response
        $address_components = $mapbox_data['features'][0]['context'];

        // Loop through the address components to find the desired information
        $street_address = '';
        $city = '';
        $state = '';
        $state_short = '';
        $country = '';
        $country_short = '';
        $zip_code = '';
        $latitude = null;
        $longitude = null;
        $full_address = $mapbox_data['features'][0]['place_name'];

        foreach ($address_components as $component) {
            try {
                if (str_contains($component['id'], 'street')) {
                    $street_address = $component['text'];
                } elseif (str_contains($component['id'], 'place')) {
                    $city = $component['text'];
                } elseif (str_contains($component['id'], 'region')) {
                    $state = $component['text'];
                    $state_short = strtoupper($component['short_code']);
                } elseif (str_contains($component['id'], 'country')) {
                    $country = $component['text'];
                    $country_short = strtoupper($component['short_code']);
                } elseif (str_contains($component['id'], 'postcode')) {
                    $zip_code = $component['text'];
                }
            } catch (\Throwable $th) {
                //throw $th;
                continue;
            }
        }

        $state_short = strtoupper(Str::slug(str_replace($country_short, '', $state_short), ''));

        $cords = static::getCordsFromData($mapbox_data);
        if ( !empty($cords) ) {
            $latitude = $cords->latitude;
            $longitude = $cords->longitude;
        }

        return (object) [
            'street_address' => $street_address,
            'city' => $city,
            'state' => $state,
            'state_short' => $state_short,
            'country' => $country,
            'country_short' => $country_short,
            'zip_code' => $zip_code,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'full_address' => $full_address
        ];
    }
}
