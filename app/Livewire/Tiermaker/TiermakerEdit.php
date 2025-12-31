<?php

namespace App\Livewire\Tiermaker;

use App\Models\Music\Artist;
use App\Models\Tiermaker\TiermakerAlbum;
use App\Models\Tiermaker\TiermakerArtist;
use Livewire\Component;

class TiermakerEdit extends Component
{
    public $albums;

    public $albumsById = [];

    public $id;

    public $artistName;

    public array $tiers = [];

    public array $labels = [];

    public array $colors = [];

    public function boot()
    {

        $this->labels = config('tiermaker.labels');

        $this->artistName = Artist::where('id', $this->id)->value('name');

        $this->albums = (new Artist)->getArtistsWithAlbums(
            [
                'name' => $this->artistName,
                'categories' => [1, 2],
            ]
        );

        // Items by ID
        foreach ($this->albums as $album) {
            $this->albumsById[$album->album_persistent_id] = [
                'persistent_id' => $album->album_persistent_id,
                'artist' => $album->name,
                'album' => $album->album_name,
                'album_image_slug' => $album->album_image_slug ?? '',
                'album_image_largest_width' => $album->album_image_largest_width ?? 0,
                'album_image_hash' => $album->album_image_hash ?? '',
            ];
        }

        // Load existing tiers from DB
        $loadedItems = TiermakerArtist::with('tiermakerAlbums.album.albumImage')
            ->where('artist_name', $this->artistName)->get()->collect();

        $albumIds = [];
        if (isset($loadedItems[0]['tiermakerAlbums']) and !$loadedItems[0]['tiermakerAlbums']->isEmpty()) {
            $albumIds = $loadedItems[0]['tiermakerAlbums'];
        }

        if (!$albumIds) {
            $this->tiers = ['pool' => array_keys($this->albumsById)];
        }

        foreach ($this->labels as $label) {
            $this->tiers[$label] = [];
        }

        if ($albumIds) {
            foreach ($albumIds as $album) {
                $this->tiers[$album->tier][] = $album->album_persistent_id;
            }
        }
    }

    public function updateTiers(array $payload): void
    {

        // Write to DB
        TiermakerAlbum::whereIn('tiermaker_id', function ($q) {
            $q->select('id')->from('tiermaker_artists')->where('artist_name', $this->artistName);
        })->delete();

        $tiermmakerArtist = TiermakerArtist::updateOrCreate(
            [
                'artist_name' => $this->artistName,
            ]
        );

        foreach ($payload as $key => $album) {
            foreach ($album as $order => $id) {
                TiermakerAlbum::updateOrCreate(
                    [
                        'tiermaker_id' => $tiermmakerArtist->id,
                        'album_persistent_id' => $id,
                        'order' => $order,
                        'tier' => $key,
                    ]
                );
            }
        }

        $this->tiers = $payload;
    }

    public function render()
    {

        return view('livewire.tiermaker.tiermaker-edit');
    }
}
