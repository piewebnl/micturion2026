<?php

namespace App\Services\Music;

use App\Helpers\ImageHelper;
use App\Models\Music\Album;
use App\Models\Music\AlbumImage;
use App\Services\Logger\Logger;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class AlbumImageSourceFinder
{
    private $album;

    private $filename = '';

    private $triedFilenames = [];

    private string $channel = 'album_create_images';

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
            $this->findAlbumArtworkFromDefaultFolder();
        }
        if (!$this->filename) {
            $this->findAlbumArtworkFromOthersFolder();
        }

        // Last try; create from m4a/aac file
        if (App::environment() == 'local' and !$this->filename) {
            $this->tryCreateFromM4aFile();
            $this->findAlbumArtworkFromDefaultFolder();
        }

        // Found nothing
        if (!$this->filename) {
            Logger::log(
                'error',
                $this->channel,
                'Album image source not found: ' . $this->album->name,
                [
                    'tried_filenames' => $this->triedFilenames,

                ]
            );
        }

        return $this->filename;
    }

    private function findAlbumArtworkFromDefaultFolder()
    {

        $this->filename = config('music.music_path') . $this->album->location . '/Folder.jpg';

        if (file_exists($this->filename)) {
            return $this->filename;
        }

        $this->triedFilenames[] = [
            'default' => $this->filename,
        ];

        $this->filename = '';
    }

    private function findAlbumArtworkFromOthersFolder()
    {

        $this->filename = config('music.album_artwork_others_path') . '/' . Str::slug($this->album->artist->name) . '/' . Str::slug($this->album->sort_name) . '/Folder.jpg';

        if (file_exists($this->filename)) {
            return $this->filename;
        }

        $this->triedFilenames[] = [
            'others' => $this->filename,
        ];

        $this->filename = '';
    }

    // Try to create a Folder.jpg from m4a file
    private function tryCreateFromM4aFile()
    {

        $albumArtworkExtractor = new AlbumArtworkExtractor;

        $folderPath = config('music.music_path') . $this->album->location;

        $filename = $albumArtworkExtractor->extractToFolderJpg($folderPath);

        if ($filename) {
            $this->filename = $filename;
        }
    }
}
