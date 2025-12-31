<?php

namespace App\Services\Images;

use Illuminate\Support\Facades\Storage;

/**
 * Deletes image from disk
 */
class ImageDeleter
{
    private $configValues;

    private $type; // image type (corresponds to type in config)

    public function __construct(string $type)
    {
        $this->type = $type;
        $this->setConfigValues();
    }

    public function setConfigValues()
    {
        $config = config('images');
        $this->configValues = $config[$this->type];

        return $this;
    }

    public function delete(string $slug): bool
    {

        // $this->largestWidth = null;
        // $this->largestHeight = null;
        // $this->slug = $slug;

        // Loop through all sizes and create
        foreach ($this->configValues['sizes'] as $size) {

            $width = $size[0];
            $height = $size[0];

            if (!Storage::disk('images')->delete($this->configValues['dest_image_path'] . '-' . $width . 'x' . $height . '/' . $slug . '.' . $this->configValues['dest_thumb_type'])) {
                return false;
            }
        }

        return true;
    }
}
