<?php

namespace App\Models\Concert;

use App\Scopes\GlobalScopesTrait;
use Illuminate\Database\Eloquent\Model;

class ConcertFestivalArtist extends Model
{
    use GlobalScopesTrait;

    protected $table = 'concert_festival_artist';

    protected $guarded = [];
}
