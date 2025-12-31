<?php

namespace App\Livewire\Forms\Music;

class MusicSearchFormInit
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
        $filterValues['sort'] ??= 'artist';
        $filterValues['order'] ??= 'asc';
        $filterValues['per_page'] ??= 100; // Set in Loadmore
        $filterValues['artist'] ??= null;
        $filterValues['show_filter'] ??= false;
        $filterValues['start_letter'] ??= null;

        $filterValues['categories'] ??= [];
        $filterValues['formats'] ??= [];
        $filterValues['genres'] ??= [];

        $filterValues['compilations'] ??= true;
        if ($filterValues['compilations'] === 'true') {
            $filterValues['compilations'] = true;
        }
        if ($filterValues['compilations'] === 'false') {
            $filterValues['compilations'] = false;
        }

        $filterValues['songs'] ??= false;
        if ($filterValues['songs'] === 'true') {
            $filterValues['songs'] = true;
        }
        if ($filterValues['songs'] === 'false') {
            $filterValues['songs'] = false;
        }

        $filterValues['spine_images_checked'] ??= true;
        if ($filterValues['spine_images_checked'] === 'true') {
            $filterValues['spine_images_checked'] = true;
        }
        if ($filterValues['spine_images_checked'] === 'false') {
            $filterValues['spine_images_checked'] = false;
        }

        $filterValues['year'] ??= null;

        return $filterValues;
    }
}
