<?php

namespace  Osoobe\Utilities\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Utilities {


    public static function setObjectValue($obj, $key, $value) {
        if ( !empty($value) ) {
            $obj->$key = $value;
        }
    }

    public static function setDataFromRequest(Request $request, $obj, $key) {
        static::setObjectValue($obj, $key, $request->input($key, null));
    }

    public static function getObjectValue($obj, $key, $default='') {
        return ( isset($obj->$key) ) ? $obj->$key : $default;
    }

    public static function getArrayValue(array $array, $key, $default='') {
        return ( isset($array[$key]) ) ? $array[$key] : $default;
    }

    public static function setArrayValue(array $array, $key, $value) {
        if ( !empty($value) ) {
            $array[$key] = $value;
        }
    }

    public static function setArrayDefault(array $array, $key, $value) {
        if ( empty($array[$key]) ) {
            $array[$key] = $value;
        }
    }

    public static function getArrayValueOrDefault($array, $key, $default_key) {
        return ( isset($array[$key]) ) ? $array[$key] : $array[$default_key];
    }

    public static function isAssociativeArray($arr) {
        return (array_values($arr) !== $arr);
    }

    public static function toAssociativeArray(array $array) {
        return array_combine($array,$array);
    }

    public static function formatDate($date, $format='Y-m-d') {
        if ( ! $date ) {
            return '';
        }
        if ( is_string($date) ) {
            return Carbon::parse($date)->format($format);
        }
        return $date->format($format);

    }


    /**
     * Get expiration date windows
     *
     * @param integer $addDays  Number of days to expire from now. Default is 45 days.
     * @param integer $subDays  Number of days the burndown date started. Default is 30 days.
     *
     * @example $e_date = AppHelper::expiryDateWindow();
     *          echo $e_date->start;
     *          echo $e_data->end;
     * @return object   expiration dates.
     */
    public static function expiryDateWindow(int $addDays=45, int $subDays=30): object {
        $now = Carbon::now();
        return (object) [
            "start" => $now->copy()->subDays($subDays),
            "end" => $now->copy()->addDays($addDays)
        ];
    }


    /**
     * calculate percentage of a number.
     *
     * @uses Formula: P% * X = Y
     *
     * @param int|double $p Percatage
     * @param int|double $n Number
     * @return int  Percentage
     */
    public static function calcNumberPercentage($p, $n) {
        return ( $p / 100 ) * $n;
    }


    /**
     * Calculate percentation
     *
     * @param int|double $q Quotient
     * @param int|double $d Divident
     * @return int  Percentage
     */
    public static function calc_percentage($q, $d) {
        if ($d == 0) {
            return 100;
        }
        return ( $q / $d ) * 100;
    }

    /**
     * Find the average of the given numbers
     *
     * @param integer ...$values
     * @return integer Returns the average of the given numbers.
     */
    public static function average(int ...$values) {
        return array_sum($values) / count($values);
    }

    /**
     * Remove zero figures and find the average of the given numbers
     *
     * @param integer ...$values
     * @return integer Returns the average of the given numbers.
     */
    public static function averageNoZero(int ...$values) {
        $nums = array_diff($values, [0]);
        return static::average(...$values);
    }




    /**
     * Categorize objects.
     *
     * @param mixed $objects
     * @param string $category
     * @return array
     */
    public static function categorizeObjects($objects, $category): array{
        $categorization = [];
        foreach($objects as $obj) {
            if ( isset($categorization[$obj->$category]) ) {
                $categorization[$obj->$category] = [$obj];
            } else {
                array_push($categorization[$obj->$category], $obj);
            }
        }
        return $categorization;
    }

    /**
     * Get hostname from /etc/hostname
     *
     * @return string
     */
    public static function getHostnameHash(): string {
        try {
            return gethostname();
        } catch (\Throwable $th) {
            return 'default';
        }

    }


    /**
     * Return value or default
     *
     * @param mixed $value
     * @param mixed $default
     * @return mixed
     */
    public static function valueOrDefault($value, $default='') {
        if ( ! $value ) {
            return '';
        }
        return $value;
    }

    /**
     * Implode nested list
     *
     * @param string $glue
     * @param array $array
     * @return string
     */
    public static function implodeNested(array $array, string $glue=','): string {
        $text = '';
        foreach ($array as $val) {
            if ( $text ) {
                $text .= $glue;
            }
            if (is_array($val)) {
                $text .= static::implodeNested($val, $glue);
            } else {
                $text .= $val;
            }
        }
        return $text;
    }

    public static function implodeWithQuotes(array $array, string $glue=','): string {
        return  '"' . implode('"'.$glue.'"', $array) . '"';
    }




        /**
     * @param $number
     * @return bool|mixed|string
     */
    public static function formatPhoneNumber($number) {

        if ( empty($number) ) {
            return false;
        }

        $number = str_replace('+','',$number);
        $number = str_replace(' ','',$number);
        $number = str_replace('-','',$number);
        $number = str_replace('(','',$number);
        $number = str_replace(')','',$number);

        // https://support.twilio.com/hc/en-us/articles/223183008-Formatting-International-Phone-Numbers
        // https://www.twilio.com/docs/glossary/what-e164#regex-matching-for-e164
        // ^[1-9]\d{1,14}$
        if (preg_match("/^[0-9]{0,5}[0-9]{10}$/i", $number)){
            if ( strlen($number) == 10 ) {
                return "+1$number";
            }
            return "+$number";
        }
        return false;
    }


    public static function csvToArray($filename = '', $delimiter = ',') {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header) {
                    $header = $row;
                } else {
                    try {
                        $data[] = array_combine($header, $row);
                    } catch (\ErrorException $e) {
                        continue;
                    }
                }
            }
            fclose($handle);
        }
        return $data;
    }


    public static function getRequestData($keys) {
        $data = collect(request()->all())->filter(function($value, $key) use ($keys) {
            if ( ! in_array($key, $keys) ) {
                return false;
            }
            return  !empty($value);
        });
        return $data->toArray();
    }

    /**
     * Get the name of the class from the class namespace.
     *
     * @param string|object|null $classname
     * @return string
     */
    public static function getClassNameOnly($classname) {
        if ( ! $classname ) {
            return '';
        }
        if ( is_object($classname) ) {
            $classname = get_class($classname);
        }
        return end(explode("\\", $classname));
    }


    public static function clientIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif( !empty($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        return "0.0.0.0";
    }

}


?>
