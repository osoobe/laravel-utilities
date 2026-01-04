<?php

namespace  Osoobe\Utilities\Helpers;

use Illuminate\Support\Facades\Storage;


class ImageHelper {


    /**
     * Store a base64 encoded image to the given path and return the file path.
     *
     * @param string $base64Image
     * @param string $directory
     * @param string|null $filename
     * @param string $disk                  See filesystems.php
     * @param string $visibility            private, public or null. If null, it will use the disk default visibility
     * @param bool $dbSave                  Save image reference to database
     * @return Image|string|false
     */
    public static function storeBase64Image(
        $base64Image,
        $directory = 'images',
        $filename = null,
        string $disk = 'public',
        ?string $visibility = null
    ) {
        // Extract the image type and base64 data
        // if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
        //     $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
        //     $extension = strtolower($type[1]);
        //     if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        //         return false;
        //     }
        // } else {
        //     return false;
        // }
        // Remove the base64 prefix if it exists
        if (str_contains($base64Image, ';base64,')) {
            [$meta, $base64Image] = explode(';base64,', $base64Image);
            $extension = explode('/', $meta)[1]; // e.g. image/png → png
        } else {
            // Default extension if no metadata is provided
            $extension = 'jpeg';
        }


        // $base64Image = str_replace(' ', '+', $base64Image);
        $imageData = base64_decode($base64Image, true);

        if ($imageData === false) {
            return false;
        }

        if (!$filename) {
            $filename = uniqid('img_', true) . '.' . $extension;
        } else {
            // Ensure the filename has the correct extension
            $filename = pathinfo($filename, PATHINFO_FILENAME) . '.' . $extension;
        }

        $path = $directory . '/' . $filename;

        $options = [];
        if ( !empty($visibility) ) {
            $options['visibility'] = $visibility;
        }

        // Store the file using Laravel's storage
        $stored = \Storage::disk($disk)
            ->put($path, $imageData, options: $options);
        return $stored ? $path : false;
    }


    /**
     * Get string after ;base64,
     *
     * @param [type] $base64Image
     * @return string|null
     */
    public static function base64Only(string $base64Image): ?string {
        $results = explode(';base64,', $base64Image);

        $len = count($results);
        if ( $len == 1 ) {
            return $results[0];
        } elseif ( $len > 1 ) {
            return $results[1];
        }
        return null;
    }


}
