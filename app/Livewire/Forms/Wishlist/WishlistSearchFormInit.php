<?php

namespace App\Livewire\Forms\Wishlist;

class WishlistSearchFormInit
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
        $filterValues['sort'] ??= 'artist_sort_name'; // artist_sort_name
        $filterValues['order'] ??= 'asc';
        $filterValues['per_page'] ??= 50; // Set in Loadmore
        $filterValues['wishlist_album'] ??= null;
        $filterValues['music_store'] ??= null;
        $filterValues['format'] ??= null;
        $filterValues['show_low_scores'] ??= false; // toggle: 0 or 1

        if ($filterValues['show_low_scores'] === 'true') {
            $filterValues['show_low_scores'] = true;
        }

        if ($filterValues['show_low_scores'] === 'false') {
            $filterValues['show_low_scores'] = false;
        }

        if (isset($filterValues['keyword']) and $filterValues['keyword'] != '') {
            $filterValues['show_low_scores'] = true;
        }

        return $filterValues;
    }
}
