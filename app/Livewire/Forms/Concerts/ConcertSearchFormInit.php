<?php

namespace App\Livewire\Forms\Concerts;

class ConcertSearchFormInit
{
    private $formData;

    public function __construct($formData)
    {
        $this->formData = $formData;
    }

    public function init(array $filterValues): array
    {

        $filterValues['page'] ??= 1;
        $filterValues['keyword'] ??= null;
        $filterValues['view'] ??= 'grid';
        $filterValues['sort'] ??= 'date';
        $filterValues['order'] ??= 'desc';
        $filterValues['per_page'] ??= 100; // Set in Loadmore
        $filterValues['name'] ??= null;
        $filterValues['year'] ??= null;
        $filterValues['venue'] ??= null;
        $filterValues['festival'] ??= null;

        return $filterValues;
    }
}
