<?php

namespace App\Livewire\Forms\Tiermaker;

class TiermakerSearchFormInit
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

        return $filterValues;
    }
}
