<?php

namespace App\Models\Wishlist;

use App\Traits\QueryCache\QueryCache;
use Illuminate\Database\Eloquent\Model;

class MusicStore extends Model
{
    use QueryCache;

    protected $guarded = [];

    public function AlbumPrice()
    {
        return $this->hasMany('App\Models\AlbumPrice');
    }

    public function getAllMusicStores()
    {
        $musicStores = $this->getCache('get-all-music-stores');

        if (!$musicStores) {
            $musicStores = MusicStore::orderBy('name', 'asc')->get();
            $this->setCache('get-all-music-stores', [], $musicStores);
        }

        return $musicStores;
    }
}
