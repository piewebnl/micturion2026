<?php

namespace App\Services\Images;

use App\Helpers\ImageHelper;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

// Create images for a certain model type

class ImageCreator
{
    private $configValues;

    private $imageType; // image type (corresponds to type in config)

    private $sourceFilename; // Full path to source

    private $slug; // Full path to destination

    private $slugWithPath; // Full path with image filename  /albums-300x300/test.jpg

    private $largestWidth;

    private $largestHeight;

    private string $hash; // md5 hash (partly) of original image [0x0]

    public function __construct(string $imageType)
    {
        $this->imageType = $imageType;
        $this->setConfigValues();

        // Create destination folders if necessery
        $createFolders = new CreateFolders($this->configValues);
        $createFolders->createFoldersIfNotExists();
    }

    public function setConfigValues()
    {
        $config = config('images');
        $this->configValues = $config[$this->imageType];

        return $this->configValues;
    }

    public function create(string $sourceFilename, string $slug): bool
    {

        $this->largestWidth = null;
        $this->largestHeight = null;
        $this->sourceFilename = $sourceFilename;
        $this->slug = $slug;

        // $imageManager = new ImageManager(['driver' => 'imagick']);
        $imageManager = new ImageManager(new Driver);

        // Loop through all sizes and create
        foreach ($this->configValues['sizes'] as $key => $size) {

            $width = $size[0];
            $height = $size[1];

            $this->generateDestinationFilenameWithPath($width, $height);

            // Source big enough?
            $sourceSize = getimagesize($this->sourceFilename);
            $img = $imageManager->read($this->sourceFilename);

            // Store original as jpg
            if ($width == 0 and $height == 0) {
                $encoded = $img->toJpeg(90);
                Storage::disk('images')->put($this->slugWithPath . '.jpg', $encoded);
            } else {
                // Resise to fixed width

                switch (true) {
                    // Resize to fixed width
                    case $sourceSize[0] >= $width && $height == 0:
                        $img = $img->scale($width, $sourceSize[1]);
                        $this->largestWidth = $width;
                        $this->largestHeight = $height;
                        break;

                        // Resize to fixed height
                    case $sourceSize[1] >= $height && $width == 0:
                        $img = $img->scale($sourceSize[0], $height);
                        $this->largestWidth = $width;
                        $this->largestHeight = $height;
                        break;

                        // Resize to cover both width and height
                    case $height > 0 && $width > 0 && ($sourceSize[0] >= $width || $sourceSize[1] >= $height):
                        $img = $img->cover($width, $height);
                        $this->largestWidth = $width;
                        $this->largestHeight = $height;
                        break;

                    default:
                        // No resize condition met
                        return false;
                }
                $encoded = $img->toWebp(90);

                Storage::disk('images')->put($this->slugWithPath . '.webp', $encoded);
            }

            // $this->createHash($this->sourceFilename);
            $this->hash = ImageHelper::createHash($this->sourceFilename);
        }

        // print_r($this->largestWidth, $this->largestHeight);

        return true;
    }

    private function generateDestinationFilenameWithPath(int $width, int $height): void
    {
        $dim = '-' . $width . 'x' . $height;
        if ($width == 0 and $height == 0) {
            $dim = '';
        }

        $this->slugWithPath = $this->configValues['dest_image_path'] . $dim . '/' . $this->slug;
    }

    public function getSlugWithPath(): ?string
    {
        return $this->slugWithPath;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function getLargestWidth(): ?int
    {
        return $this->largestWidth;
    }

    public function getLargestHeight(): ?int
    {
        return $this->largestHeight;
    }
}
