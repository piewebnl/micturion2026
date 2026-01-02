<?php

namespace App\Models\Concert;

use App\Services\Concert\ConcertFestivalImageSourceFinder;
use App\Services\Images\ImageCreator;
use App\Traits\Logger\Logger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ConcertFestivalImage extends Model
{
    protected $guarded = [];

    protected $type = 'concert_festival';

    private string $channel = 'concert_festival_create_images';

    private $concertFestival;

    private $slug;

    public function concertFestival()
    {
        return $this->hasOne(ConcertFestival::class);
    }

    public function create(ConcertFestival $concertFestival, $uploadFile = null)
    {
        $this->concertFestival = $concertFestival;
        $imageCreator = new ImageCreator($this->type);

        if ($uploadFile) {
            $source = $uploadFile;
        } else {
            $concertImageSourceFinder = new ConcertFestivalImageSourceFinder($this->concertFestival);
            $source = $concertImageSourceFinder->findFilename();
            if (!$source) {
                return;
            }
        }

        $this->slug = $this->getConcertFestivalImageSlug($this->concertFestival);

        $create = false;
        if ($uploadFile) {
            $create = true;
        }
        if (!$uploadFile && $concertImageSourceFinder->isSourceModified($this->concertFestival->concertFestivalImage?->hash)) {
            $create = true;
        }
        if (!$create and $this->existsInDb()) {
            Logger::log(
                'info',
                $this->channel,
                'Concert festival image not chagned: ' . $this->concertFestival->name . ' [' . $this->concertFestival->concert->date . ']'
            );
            return;
        }

        $imageCreator->create($source, $this->slug);
        $hash = $imageCreator->getHash();
        $largestWidth = $imageCreator->getLargestWidth();
        $largestHeight = $imageCreator->getLargestHeight();

        ConcertFestivalImage::updateOrCreate(
            ['concert_festival_id' => $this->concertFestival->id],
            [
                'slug' => $this->slug,
                'largest_width' => $largestWidth,
                'largest_height' => $largestHeight,
                'hash' => $hash,
            ]
        );

        Logger::log(
            'notice',
            $this->channel,
            'Concert festival image created: ' . $this->concertFestival->name . ' [' . $this->concertFestival->concert->date . ']'
        );

        return true;
    }

    public function existsInDb()
    {

        if ($this->concertFestival->concertFestivalImage !== null) {
            return true;
        }

        return false;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getConcertFestivalImageSlug(ConcertFestival $concertFestival): string
    {
        return Str::slug($concertFestival->name . '-' . $concertFestival->id);
    }
}
