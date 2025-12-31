<?php

namespace App\Livewire\Forms\LastFm;

class LastFmSearchFormInit
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
        $filterValues['per_page'] ??= 50; // Set in Loadmore
        $filterValues['album'] ??= null;

        return $filterValues;
    }
}
