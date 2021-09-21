<?php

namespace  Osoobe\Utilities\Helpers;

use Illuminate\Support\Str as ParentStr;

class Str extends ParentStr {

    public static function ucwords($text) {
        return ucwords(strtolower($text));
    }


}

?>
