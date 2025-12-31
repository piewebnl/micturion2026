<?php

namespace App\Models\Concert;

use App\Scopes\GlobalScopesTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class ConcertItem extends Model implements Sortable
{
    use GlobalScopesTrait;
    use SortableTrait;

    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];

    public $with = ['concertArtist', 'concertImage'];

    protected $guarded = [];

    public function concert()
    {
        return $this->belongsTo(Concert::class, 'concert_id', 'id');
    }

    public function concertArtist()
    {
        return $this->belongsTo(ConcertArtist::class, 'concert_artist_id', 'id');
    }

    public function concertImage()
    {
        return $this->hasOne(ConcertImage::class);
    }

    public function storeImages(ConcertItem $concertItem, array $images = [])
    {
        foreach ($images as $image) {
            $imageCreator = new ConcertImage;
            $imageCreator->create($concertItem, $image->getPath() . '/' . $image->getFilename());
        }
    }
}
