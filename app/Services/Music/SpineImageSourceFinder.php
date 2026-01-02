<?php

namespace App\Services\Music;

use App\Helpers\ImageHelper;
use App\Models\Music\Album;
use App\Models\Music\AlbumImage;
use App\Traits\Logger\Logger;
use Illuminate\Support\Str;

class SpineImageSourceFinder
{
    private $album;

    private $filename = '';

    private $triedFilenames = [];

    private string $channel = 'spine_image_create_images';

    public function __construct(Album $album)
    {
        $this->album = $album;
    }

    public function isSourceModified($hashFromDb): bool
    {
        $fileHash = ImageHelper::createHash($this->filename);

        if ($hashFromDb != $fileHash) {
            return true;
        }

        return false;
    }

    public function findFilename(): ?string
    {
        $albumImage = new AlbumImage;

        if (!$this->filename) {
            $this->findSpineFromDefaultFolder();
        }
        if (!$this->filename) {
            $this->findSpineFromExtractedFolder();
        }

        // Found nothing
        if (!$this->filename) {
            Logger::log(
                'error',
                $this->channel,
                'Spine image source not found: ' . $this->album->name,
                [
                    'tried_filenames' => $this->triedFilenames,

                ]
            );
        }

        return $this->filename;
    }

    private function findSpineFromDefaultFolder()
    {

        $this->filename = config('music.spine_images_path') . $this->album->location . 'Folder.jpg';

        if (file_exists($this->filename)) {
            return $this->filename;
        }

        $this->triedFilenames[] = [
            'default' => $this->filename,
        ];

        $this->filename = '';
    }

    private function findSpineFromExtractedFolder()
    {

        $slug = Str::slug($this->album->artist->name) . '-' . Str::slug($this->album->sort_name) . '-' . $this->album->discogsReleases[0]->release_id . '.jpg';

        $this->filename = storage_path() . config('music.spine_images_extracted_path') . '/' . $slug;

        if (file_exists($this->filename)) {
            return $this->filename;
        }

        $this->triedFilenames[] = [
            'spine_extracted' => $this->filename,
        ];

        $this->filename = '';
    }
}
