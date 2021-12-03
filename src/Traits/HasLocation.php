<?php

namespace  Osoobe\Utilities\Traits;

use Osoobe\Utilities\Helpers\Utilities;

trait HasLocation {


    /**
     * Get the given object's state and country address.
     *
     * @return string
     */
    public function getStateAddress() {
        $state = Utilities::getObjectValue($this, 'state', '');
        $country = Utilities::getObjectValue($this, 'country', '');
        return "$state $country";
    }

    /**
     * Get the given object's city, state and country address.
     *
     * @return string
     */
    public function getCityAddress() {
        $city = Utilities::getObjectValue($this, 'city', '');
        $state_address = $this->getStateAddress();
        return "$city $state_address";
    }


    /**
     * Get the given object's street, city, state and country address.
     *
     * @return string
     */
    public function getFullAddress() {
        $street_address = Utilities::getObjectValue($this, 'street_address', '');
        $city_address = $this->getCityAddress();
        return "$street_address $city_address";
    }


    /**
     * Get the given object's street, city, state and country address and zip code.
     *
     * @return string
     */
    public function getFullAddressWithZipCode() {
        $zip_code = Utilities::getObjectValue($this, 'zip_code', '');
        $full_address = $this->getFullAddress();
        return "$full_address $zip_code";
    }

    public function getGoogleMapLinkAttribute() {
        return "http://maps.google.com/?q=".$this->getFullAddress();
    }


    /**
     * Get address data as array
     *
     * @return void
     */
    public function getAddressArray() {
        return [
            "street" => $this->street_address,
            "state" => $this->state,
            "city" => $this->city,
            "zip" => $this->zip_code,
            "country" => $this->country,
        ];
    }

}
