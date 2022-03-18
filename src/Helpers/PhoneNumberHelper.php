<?php

namespace  Osoobe\Utilities\Helpers;

class PhoneNumberHelper {

    /**
     * Check if phone number is valid
     *
     * @param string $phone_number
     * @return boolean
     */
    public static function isValid($phone_number): bool {
        return preg_match(
            config(
                'validation.phone.pattern',
                '%^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\./0-9]*$%i'
            ), $phone_number
        ) && strlen($phone_number) >= 10;
    }

}


?>
