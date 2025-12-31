<?php

namespace App\Livewire\Forms\Discogs;

use App\Helpers\SearchFormHelper;

class DiscogsSearchFormInit
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

        $filterValues['formats'] ??= SearchFormHelper::checkByLabel($this->formData['formats'], ['CD', 'LP', '7inch', '10inch', '12inch', 'CAS', 'DVD', 'VHS']);

        $filterValues['matched'] ??= 'all';

        $filterValues['without_discogs'] ??= false;
        if ($filterValues['without_discogs'] === 'true') {
            $filterValues['without_discogs'] = true;
        }
        if ($filterValues['without_discogs'] === 'false') {
            $filterValues['without_discogs'] = false;
        }

        $filterValues['show_notes'] ??= false;
        $filterValues['show_notes'] = SearchFormHelper::check_bool($filterValues['show_notes'] ?? null);

        return $filterValues;
    }
}
