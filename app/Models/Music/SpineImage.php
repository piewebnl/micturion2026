<?php

namespace App\Models\Music;

use App\Services\Images\ImageCreator;
use App\Services\Music\SpineImageSourceFinder;
use App\Traits\Logger\Logger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SpineImage extends Model
{
    protected $guarded = [];

    protected $type = 'spine';

    private $album;

    private $channel = 'spine_image_create_images';

    public function album()
    {
        return $this->hasOne(Album::class);
    }

    public function create(Album $album, $uploadFile = null)
    {

        $this->album = $album;

        $imageCreator = new ImageCreator($this->type);

        if ($uploadFile) {
            $source = $uploadFile;
        } else {
            $albumImageSourceFinder = new SpineImageSourceFinder($this->album);
            $source = $albumImageSourceFinder->findFilename();
            if (!$source) {
                return;
            }
        }

        $this->slug = $this->getSpineImageSlug($this->album);

        $create = false;
        if ($uploadFile) {
            $create = true;
        }
        if (!$uploadFile && $albumImageSourceFinder->isSourceModified($this->album->albumImage?->hash)) {
            $create = true;
        }
        if (!$create and $this->existsInDb()) {
            Logger::log(
                'info',
                $this->channel,
                'Spine image already exists: ' . $this->album->artist->name . ' - ' . $this->album->name
            );

            return;
        }

        $imageCreator->create($source, $this->slug);
        $hash = $imageCreator->getHash();
        $largestWidth = $imageCreator->getLargestWidth();
        $largestHeight = $imageCreator->getLargestHeight();

        SpineImage::updateOrCreate(
            ['album_id' => $this->album->id],
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
            'Spine image created: ' . $this->album->artist->name . ' - ' . $this->album->name
        );
    }


    public function existsInDb()
    {

        if ($this->album->albumImage !== null) {
            return true;
        }

        return false;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getSpineImageSlug(Album $album): string
    {
        return Str::slug($album->artist->name) . '-' . Str::slug($album->sort_name);
    }
}
