<?php

namespace App\Services\Concert;

use App\Helpers\ImageHelper;
use App\Models\Concert\ConcertFestival;
use App\Models\Concert\ConcertFestivalImage;
use App\Services\Logger\Logger;

class ConcertFestivalImageSourceFinder
{
    private $concertFestival;

    private $isFound = false;

    private $filename = '';

    private string $channel = 'concert_festival_create_images';

    public function __construct(ConcertFestival $concertFestival)
    {
        $this->concertFestival = $concertFestival;
    }

    public function isSourcefound(): bool
    {
        if ($this->isFound) {
            return true;
        }

        return false;
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
        $concertFestivalImage = new ConcertFestivalImage;
        $slug = $concertFestivalImage->getConcertFestivalImageSlug($this->concertFestival);

        $this->filename = config('concerts.concert_festival_images_path') . '/' . $slug . '.jpg';

        if (file_exists($this->filename)) {
            $this->isFound = true;
        } else {

            Logger::log(
                'error',
                $this->channel,
                'Concert festival image source not found: ' . $this->concertFestival->name . ' [' . $this->concertFestival->concert->date . ']',
                [
                    'filename' => $this->filename,
                ]
            );

            return null;
        }

        return $this->filename;
    }
}
