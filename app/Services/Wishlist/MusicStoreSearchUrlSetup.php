<?php

namespace App\Services\Wishlist;

class MusicStoreSearchUrlSetup
{
    public function setup(string $url, string $artist, string $album)
    {

        $artist = strtolower($artist);
        $album = strtolower($album);

        $artist = str_replace(' ', '+', $artist);
        $album = str_replace(' ', '+', $album);
        $url = str_replace(' ', '+', $url);

        $url = str_replace('[SEARCH_ARTIST]', $artist, $url);
        $url = str_replace('[SEARCH_ALBUM]', $album, $url);

        return $url;
    }
}
