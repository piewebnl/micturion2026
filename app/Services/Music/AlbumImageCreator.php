<?php

namespace App\Services\Music;

use App\Models\Music\Album;
use App\Models\Music\AlbumImage;

// Create concert image (via upload or found on disk)
class AlbumImageCreator
{
    public function createAlbumImage(int $id)
    {

        $album = Album::with(['Artist', 'Category'])->find($id);

        if ($album) {

            $albumImage = new AlbumImage;
            $albumImage->create($album);
        }
    }
}
