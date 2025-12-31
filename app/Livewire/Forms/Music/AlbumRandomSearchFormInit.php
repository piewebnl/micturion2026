<?php

namespace App\Livewire\Forms\Music;

use App\Helpers\SearchFormHelper;

class AlbumRandomSearchFormInit
{
    private $formData;

    public function __construct($formData)
    {
        $this->formData = $formData;
    }

    public function init(array $filterValues): array
    {

        $filterValues['page'] = 1;
        $filterValues['view'] = 'grid';
        $filterValues['order'] = 'asc';
        $filterValues['sort'] ??= 'random';
        $filterValues['per_page'] = 10;

        // $filterValues['categories'] ??= SearchFormHelper::checkAll($this->formData['categories']);
        $filterValues['categories'] ??= SearchFormHelper::checkByLabel($this->formData['categories'], ['Albums']);
        $filterValues['formats'] ??= SearchFormHelper::checkByLabel($this->formData['formats'], ['CD', 'LP']);

        $filterValues['compilations'] ??= true;

        if ($filterValues['compilations'] === 'true') {
            $filterValues['compilations'] = true;
        }

        if ($filterValues['compilations'] === 'false') {
            $filterValues['compilations'] = false;
        }

        return $filterValues;
    }
}
