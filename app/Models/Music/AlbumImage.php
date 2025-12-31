<?php

namespace App\Models\Music;

use App\Services\Images\ImageCreator;
use App\Services\Music\AlbumImageSourceFinder;
use App\Traits\Logger\Logger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AlbumImage extends Model
{
    protected $guarded = [];

    protected $type = 'album';

    private string $channel = 'album_create_images';

    public function album()
    {
        return $this->hasOne(Album::class);
    }

    public function create(Album $album, $uploadFile = null)
    {

        $this->album = $album;

        if ($this->album->category->image_type == 'video') {
            $this->type = 'video';
        }

        $imageCreator = new ImageCreator($this->type);

        if ($uploadFile) {
            $source = $uploadFile;
        } else {
            $albumImageSourceFinder = new AlbumImageSourceFinder($this->album);
            $source = $albumImageSourceFinder->findFilename();
            if (!$source) {
                return;
            }
        }

        $this->slug = $this->getAlbumImageSlug($this->album);

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
                'Album image already exists: ' . $this->album->artist->name . ' - ' . $this->album->name
            );

            return;
        }

        $imageCreator->create($source, $this->slug);
        $hash = $imageCreator->getHash();
        $largestWidth = $imageCreator->getLargestWidth();
        $largestHeight = $imageCreator->getLargestHeight();

        AlbumImage::updateOrCreate(
            ['album_id' => $this->album->id],
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
            'Album image created: ' . $this->album->artist->name . ' - ' . $this->album->name
        );
    }

    /*
    public function remove(Album $album, $slug)
    {
        $imageDeleter = new ImageDeleter($this->type);
        $imageDeleter->delete($albumImage->slug);

        // Delete in db
        $albumImage = ConcertImage::destroy($album->id);

        $this->response = response()->success('Concert image deleted ' . $this->album->name);

        Logger::log('info', $this->channel, 'Concert image deleted  ' . $this->album->name);

        return $albumImage;
    }
        */

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

    public function getAlbumImageSlug(Album $album): string
    {
        return Str::slug($album->artist->sort_name) . '/' . Str::slug($album->sort_name);
    }
}
