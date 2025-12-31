<?php

namespace App\Livewire\Forms\Spotify;

class SpotifyReviewSearchFormInit
{
    private $formData;

    public function __construct($formData)
    {
        $this->formData = $formData;
    }

    public function init(array $filterValues): array
    {

        $filterValues['page'] ??= 1;
        $filterValues['view'] ??= 'track'; // or album
        $filterValues['keyword'] ??= null;
        $filterValues['sort'] ??= 'artist';
        $filterValues['status'] ??= null;
        $filterValues['order'] ??= 'asc';
        $filterValues['per_page'] ??= 50; // Set in Loadmore

        return $filterValues;
    }
}
