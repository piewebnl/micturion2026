<?php

namespace App\Livewire\Forms\Spotify;

class SpotifyMonthlyPlaylistSearchFormInit
{
    private $formData;

    public function __construct($formData)
    {
        $this->formData = $formData;
    }

    public function init(array $filterValues): array
    {

        $filterValues['page'] ??= 1;
        $filterValues['view'] ??= 'grid';
        $filterValues['keyword'] ??= null;
        $filterValues['year'] ??= null;
        $filterValues['sort'] ??= 'name'; // Playlist name (=date)
        $filterValues['order'] ??= 'desc';
        $filterValues['per_page'] ??= 100; // Set in Loadmore
        $filterValues['name'] = 'Playlist 20';
        $filterValues['album_id'] ??= [];

        return $filterValues;
    }
}
