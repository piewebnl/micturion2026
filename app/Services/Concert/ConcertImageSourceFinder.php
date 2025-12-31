<?php

namespace App\Services\Concert;

use App\Helpers\ImageHelper;
use App\Models\Concert\ConcertImage;
use App\Models\Concert\ConcertItem;
use App\Traits\Logger\Logger;

class ConcertImageSourceFinder
{
    private $concertItem;

    private $isFound = false;

    private $filename = '';

    private string $channel = 'concert_create_images';

    public function __construct(ConcertItem $concertItem)
    {
        $this->concertItem = $concertItem;
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
        $concertImage = new ConcertImage;
        $slug = $concertImage->getConcertImageSlug($this->concertItem);

        $this->filename = config('concerts.concert_images_path') . '/' . $slug . '.jpg';

        if (file_exists($this->filename)) {
            $this->isFound = true;
        } else {

            // Give support no error
            $status = 'error';
            $info = '';
            if ($this->concertItem->support) {
                $status = 'warning';
                $info = ' (Support) ';
            }

            Logger::log(
                $status,
                $this->channel,
                'Concert image ' . $info . 'source not found: ' . $this->concertItem->concertArtist->name . ' [' . $this->concertItem->concert->date . ']',
                [
                    'filename' => $this->filename,
                ]
            );

            return null;
        }

        return $this->filename;
    }
}
