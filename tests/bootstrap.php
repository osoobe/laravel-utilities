<?php

if (!function_exists('array_to_object')) {
    function array_to_object(array $arr): object
    {
        return (object) $arr;
    }
}
