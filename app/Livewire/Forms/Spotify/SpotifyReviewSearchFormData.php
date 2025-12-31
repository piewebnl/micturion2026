<?php

namespace App\Livewire\Forms\Spotify;

use App\Traits\QueryCache\QueryCache;

class SpotifyReviewSearchFormData
{
    use QueryCache;

    public function generate(): array
    {

        $formData['sort'] = [
            ['value' => 'date', 'label' => 'Date', 'order' => 'desc'],
            ['value' => 'name', 'label' => 'Name', 'order' => 'asc'],
        ];

        $formData['view'] = [
            ['value' => 'track', 'label' => 'Track'],
            ['value' => 'album', 'label' => 'Album'],
        ];

        $formData['status'] = [
            ['value' => null, 'label' => 'All'],
            ['value' => 'success', 'label' => 'Success'],
            ['value' => 'error', 'label' => 'Error'],
        ];

        $formData['order_toggle_icon'] = 'up';

        return $formData;
    }
}
