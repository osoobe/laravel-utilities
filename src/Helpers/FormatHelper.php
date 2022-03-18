<?php

namespace  Osoobe\Utilities\Helpers;

class FormatHelper {

    const SUPPORTED_FORMATS = ['html', 'markdown', 'string'];
    protected const REMOVE_PATTERN = '/(mailto\:|tel\:)/i';


    /**
     * Format string
     *
     * @param string $string
     * @param string $format    Options are `markdown`, `html` and `string`. Default is `string`
     * @param string $title     Title for the string
     * @return string
     */
    public static function formatString(string $string, string $format=null, string $title=null): string {
        if ( !empty($title) ) {
            Str::headline($title);
        }

        if ( filter_var($string, FILTER_VALIDATE_URL) ) {
            return static::formatLink($string, $format, $title, false);
        } elseif ( filter_var($string, FILTER_VALIDATE_EMAIL) ) {
            return static::formatEmail($string, $format, $title, false);
        } elseif ( PhoneNumberHelper::isValid($string) ) {
            return static::formatPhone($string, $format, $title, false);
        }
        return static::__formatString($string, $format, $title);
    }

    /**
     * Format link
     *
     * @param string $string
     * @param string $format    Options are `markdown`, `html` and `string`. Default is `string`
     * @param string $title     Title for the string
     * @param bool $check       Check format
     * @return string
     */
    public static function formatLink(
        string $string, string $format=null, string $title=null, bool $check=true
    ): string {
        if ( !empty($title) ) {
            Str::headline($title);
        }

        if ( $check && ! filter_var($string, FILTER_VALIDATE_URL) ) {
            return static::__formatString($string, $format, $title);
        }

        switch (strtolower((string) $format)) {
            case 'html':
                if ( empty($title) ) {
                    $title = preg_replace(static::REMOVE_PATTERN, "", $string);
                    return "<a class='lm-format' href='$string'>$title</a>";
                }
                return "<a class='lm-format' href='$string'>$title</a>";
            case 'markdown':
                if ( empty($title) ) {
                    $title = preg_replace(static::REMOVE_PATTERN, "", $string);
                    return "[$title]($string)";
                }
                return "[$title]($string)";
            default:
                return static::__formatString($string, $format, $title);
        }
    }


    /**
     * Format email
     *
     * @param string $string
     * @param string $format    Options are `markdown`, `html` and `string`. Default is `string`
     * @param string $title     Title for the string
     * @param bool $check       Check format
     * @return string
     */
    public static function formatEmail(
        string $string, string $format=null, string $title=null, $check=true
    ): string {
        if ( ! in_array(strtolower($format), static::SUPPORTED_FORMATS) ) {
            return static::__formatString($string, $format, $title);
        }
        if ( $check ) {
            if ( ! filter_var($string, FILTER_VALIDATE_EMAIL) ) {
                return static::__formatString($string, $format, $title);
            }
        }
        return static::formatLink("mailto:$string", $format, $title, false);
    }

    /**
     * Format phone number
     *
     * @param string $string
     * @param string $format    Options are `markdown`, `html` and `string`. Default is `string`
     * @param string $title     Title for the string
     * @param bool $check       Check format
     * @return string
     */
    public static function formatPhone(
        string $string, string $format=null, string $title=null, bool $check=true
    ) {
        if ( ! in_array(strtolower($format), static::SUPPORTED_FORMATS) ) {
            return static::__formatString($string, $format, $title);
        }
        if ( $check ) {
            if ( ! PhoneNumberHelper::isValid($string) ) {
                return static::__formatString($string, $format, $title);
            }
        }
        return static::formatLink("tel:$string", $format, $title, false);
    }


    /**
     * Format string
     *
     * @param string $string
     * @param string $format    Options are `markdown`, `html` and `string`. Default is `string`
     * @param string $title     Title for the string
     * @return string
     */
    protected static function __formatString(string $string, string $format=null, string $title=null): string {
        if ( !empty($title) ) {
            Str::headline($title);
        }

        switch (strtolower((string) $format)) {
            case 'html':
                if ( empty($title) ) {
                    return $string;
                }
                return "<strong>$title</string>: <span>$string</span>";
            case 'markdown':
                if ( empty($title) ) {
                    $string = preg_replace(static::REMOVE_PATTERN, "", $string);
                    return $string;
                }
                return "**$title**:     $string";
            default:
                if ( empty($title) ) {
                    return $string;
                }
                return "$title:     $string";
        }
    }



}

?>
