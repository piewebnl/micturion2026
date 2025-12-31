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
        if (!$uploadFile && $concertImageSourceFinder->isSourceModified($this->concertFestival->concertImage?->hash)) {
            $create = true;
        }
        if (!$create and $this->existsInDb()) {
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
            'info',
            $this->channel,
            'Concert festival image created: ' . $this->concertFestival->name . ' [' . $this->concertFestival->concert->date . ']'
        );
    }

    /*
    public function remove(ConcertFestival $concertFestival, $slug)
    {
        $imageDeleter = new ImageDeleter($this->type);
        $imageDeleter->delete($concertImage->slug);

        // Delete in db
        $concertFestivalImage = ConcertImage::destroy($concertFestival->id);

        $this->response = response()->success('Concert image deleted ' . $this->concertFestival->name);

        Logger::log('info', $this->channel, 'Concert image deleted  ' . $this->concertFestival->name);

        return $concertFestivalImage;
    }
        */

    public function existsInDb()
    {

        if ($this->concertFestival->concertImage !== null) {
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
