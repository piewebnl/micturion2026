<?php

namespace App\Models\Music;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AlbumFormats extends Model
{
    protected $guarded = [];

    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id', 'id');
    }

    public function getOwned()
    {

        $owned =
            AlbumFormats::select('formats.name', DB::raw('COUNT(formats.name) AS format_count'))
                ->join(
                    'formats',
                    'album_formats.format_id',
                    'formats.id'
                )->whereNull('parent_id')->groupBy('formats.name')->get();

        return $owned;
    }
}
