<?php

namespace App\Services\Images;

use Illuminate\Support\Facades\Storage;

// Create image folder of a certain image type
class CreateFolders
{
    private $configValues;

    public function __construct(array $configValues)
    {
        $this->configValues = $configValues;
    }

    public function createFoldersIfNotExists(): void
    {

        foreach ($this->configValues['sizes'] as $size) {
            $dim = '-' . $size[0] . 'x' . $size[1];
            if ($size[0] == 0 and $size[1] == 0) {
                $dim = '';
            }

            $folder = $this->configValues['dest_image_path'] . $dim;

            if (!Storage::disk('images')->has($folder)) {
                Storage::disk('images')->makeDirectory($folder);
            }
        }
    }
}
