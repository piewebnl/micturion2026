<?php

namespace App\Helpers;

class ImageHelper
{
    public static function createHash(string $filename)
    {
        if (file_exists($filename)) {
            return substr(hash('sha256', filemtime($filename)), 0, 16);
        }
    }

    public static function findLargestSizes($sizes, $sourceFilename)
    {

        // Loop through all sizes and create
        foreach ($sizes as $key => $size) {

            $width = $size[0];
            $height = $size[1];

            // Source big enough?
            $sourceSize = getimagesize($sourceFilename);
            // $img = $imageManager->read($sourceFilename);

            if ($width == 0 and $height == 0) {
                // Original
            } else {
                // Resise to fixed width

                switch (true) {
                    case $sourceSize[0] >= $width && $height == 0:
                        $largestWidth = $width;
                        $largestHeight = $sourceSize[1];
                        break;

                        // Resize to fixed height
                    case $sourceSize[1] >= $height && $width == 0:
                        $largestWidth = $sourceSize[0];
                        $largestHeight = $height;
                        break;

                        // Resize to cover both width and height
                    case $height > 0 && $width > 0 && ($sourceSize[0] >= $width || $sourceSize[1] >= $height):

                        $largestWidth = $width;
                        $largestHeight = $height;
                        break;

                    default:
                        // No resize condition met
                        return false;
                }
            }
        }

        return [$largestWidth, $largestHeight];
    }
}
