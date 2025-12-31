<?php

namespace App\Models\Tiermaker;

use App\Models\Music\Album;
use App\Scopes\GlobalScopesTrait;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Database\Eloquent\Model;

class TiermakerAlbum extends Model
{
    use GlobalScopesTrait;
    use QueryCache;

    protected $guarded = [];

    public function album()
    {
        return $this->belongsTo(Album::class, 'album_persistent_id', 'persistent_id');
    }
}
