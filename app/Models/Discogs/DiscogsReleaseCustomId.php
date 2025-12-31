<?php

namespace App\Models\Discogs;

use Illuminate\Database\Eloquent\Model;

class DiscogsReleaseCustomId extends Model
{
    protected $table = 'discogs_release_custom_ids';

    protected $guarded = [];

    public function store(discogsReleaseCustomId $discogsReleaseCustomId)
    {
        $result = discogsReleaseCustomId::updateOrCreate(
            [
                'persistent_album_id' => $discogsReleaseCustomId['persistent_album_id'],
            ],
            [
                'release_id' => $discogsReleaseCustomId['release_id'],
                'artist' => $discogsReleaseCustomId['artist'],
                'title' => $discogsReleaseCustomId['title'],
            ]
        );

        return $result;
    }
}
