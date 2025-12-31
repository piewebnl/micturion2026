<?php

namespace App\Models\Music;

use App\Models\Wishlist\MusicStore;
use App\Scopes\GlobalScopesTrait;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Database\Eloquent\Model;
use Znck\Eloquent\Traits\BelongsToThrough;

class AlbumPurchase extends Model
{
    use BelongsToThrough;
    use GlobalScopesTrait;
    use QueryCache;

    protected $guarded = [];

    public function album()
    {
        return $this->hasOne(Album::class, 'id', 'album_id');
    }

    public function artist()
    {
        return $this->belongsToThrough(Artist::class, Album::class);
    }

    public function format()
    {
        return $this->belongsTo(Format::class);
    }

    public function musicStore()
    {
        return $this->belongsTo(MusicStore::class);
    }
}
