<?php

namespace App\Livewire\Forms\Music;

class AlbumWithoutDiscogsSearchFormInit
{
    private $formData;

    public function __construct($formData)
    {
        $this->formData = $formData;
    }

    public function init(array $filterValues): array
    {

        $filterValues['page'] ??= 1;
        $filterValues['order'] ??= 'asc';
        $filterValues['per_page'] ??= 100; // Set in Loadmore
        $filterValues['keyword'] ??= null;

        $filterValues['matched'] ??= 'all';

        return $filterValues;
    }
}
