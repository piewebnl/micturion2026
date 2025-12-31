<?php

namespace App\Services\Music;

use App\Models\Music\Album;
use App\Models\Music\SpineImage;

// Create concert image (via upload or found on disk)
class SpineImageCreator
{
    public function createSpineImage(int $id)
    {

        $album = Album::where('id', $id)
            ->whereHas('discogsReleases', function ($q) {
                $q->where('format', 'cd');
            })
            ->with('artist', 'discogsReleases')
            ->first();

        if ($album) {
            $spineImage = new SpineImage;
            $spineImage->create($album);
        }
    }
}
