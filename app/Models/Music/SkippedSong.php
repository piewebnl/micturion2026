<?php

namespace App\Models\Music;

use App\Scopes\GlobalScopesTrait;
use Illuminate\Database\Eloquent\Model;

class SkippedSong extends Model
{
    use GlobalScopesTrait;

    // iTunes field with corresponing model fields
    protected $fields = [
        'persistent_id' => 'persistent_id',
        'track_id' => 'track_id', // iTunes Track ID
        'name' => 'name',

        'album' => 'album_name',
        'artist' => 'artist_name',

        'track_number' => 'track_number',
        'track_count' => 'track_count',
        'disc_count' => 'disc_count',
        'disc_number' => 'disc_number',

        'grouping' => 'grouping',
        'time' => 'time',
        'rating' => 'rating',
        'play_count' => 'play_count',

        'comments' => 'comments',
        'location' => 'location',
        'size' => 'size',
        'kind' => 'kind',

        'has_changed' => 'has_changed',
        'date_added' => 'date_added',
        'date_modified' => 'date_modified',
    ];

    protected $guarded = [];

    public function storeItunesLibraryTrack(object $itunesTrack)
    {
        if ($itunesTrack->name != null) {
            $songSkipped = SkippedSong::firstOrNew([
                'persistent_id' => $itunesTrack->persistent_id,
            ]);

            foreach ($this->fields as $key => $field) {
                $songSkipped->{$field} = $itunesTrack->{$key};
            }

            $songSkipped->save();

            return $songSkipped->id;
        }
    }
}
