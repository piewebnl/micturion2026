<?php

namespace App\Models\Tiermaker;

use App\Models\Music\Artist;
use App\Scopes\GlobalScopesTrait;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Database\Eloquent\Model;

class TiermakerArtist extends Model
{
    use GlobalScopesTrait;
    use QueryCache;

    protected $guarded = [];

    public function artist()
    {
        return $this->belongsTo(Artist::class, 'artist_name', 'name');
    }

    public function tiermakerAlbums()
    {
        return $this->hasMany(TiermakerAlbum::class, 'tiermaker_id', 'id');
    }
}
