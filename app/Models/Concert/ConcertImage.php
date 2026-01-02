<?php

namespace App\Models\Concert;

use App\Services\Concert\ConcertImageSourceFinder;
use App\Services\Images\ImageCreator;
use App\Traits\Logger\Logger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ConcertImage extends Model
{
    protected $guarded = [];

    protected $type = 'concert';

    private $slug;

    private $concertItem;

    private string $channel = 'concert_create_images';

    public function concert()
    {
        return $this->hasOne(ConcertItem::class);
    }

    public function create(ConcertItem $concertItem, $uploadFile = null)
    {
        $this->concertItem = $concertItem;
        $imageCreator = new ImageCreator($this->type);

        if ($uploadFile) {
            $source = $uploadFile;
        } else {
            $concertImageSourceFinder = new ConcertImageSourceFinder($this->concertItem);
            $source = $concertImageSourceFinder->findFilename();
            if (!$source) {
                return;
            }
        }

        $this->slug = $this->getConcertImageSlug($this->concertItem);

        $create = false;
        if ($uploadFile) {
            $create = true;
        }
        if (!$uploadFile && $concertImageSourceFinder->isSourceModified($this->concertItem->concertImage?->hash)) {
            $create = true;
        }
        if (!$create and $this->existsInDb()) {
            Logger::log(
                'info',
                $this->channel,
                'Concert image not chagned: ' . $this->concertItem->name . ' [' . $this->concertItem->concert->date . ']'
            );

            return;
        }

        $imageCreator->create($source, $this->slug);
        $hash = $imageCreator->getHash();
        $largestWidth = $imageCreator->getLargestWidth();
        $largestHeight = $imageCreator->getLargestHeight();

        ConcertImage::updateOrCreate(
            ['concert_item_id' => $this->concertItem->id],
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
            'Concert image created: ' . $this->concertItem->name . ' [' . $this->concertItem->concert->date . ']'
        );

        return true;
    }

    public function existsInDb()
    {

        if ($this->concertItem->concertImage !== null) {
            return true;
        }

        return false;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getConcertImageSlug($concertItem): string
    {
        return date('Y-m-d', strtotime($concertItem->concert->date)) . '-' . Str::slug($concertItem->concertArtist->name) . '-' . $concertItem->id;
    }
}
