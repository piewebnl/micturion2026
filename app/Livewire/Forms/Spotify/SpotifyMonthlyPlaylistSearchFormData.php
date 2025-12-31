<?php

namespace App\Livewire\Forms\Spotify;

use App\Models\Spotify\SpotifyPlaylist;
use App\Services\Forms\FormDataGenerator;
use App\Traits\QueryCache\QueryCache;

class SpotifyMonthlyPlaylistSearchFormData
{
    use QueryCache;

    public function generate(): array
    {
        $formDataGenerator = new FormDataGenerator;

        $items = SpotifyPlaylist::selectRaw('substr(date,1,4) as years')->groupBy('years')->whereNotNull('date')->orderBy('years', 'desc')->get();
        $formDataGenerator->setLabel(['years']);
        $formDataGenerator->setValue('years');
        $formData['years'] = $formDataGenerator->generate($items);

        $formData['view'] = [
            ['value' => 'grid', 'label' => 'List', 'icon' => 'list'],
            ['value' => 'table', 'label' => 'Table', 'icon' => 'table'],
        ];

        $formData['order_toggle_icon'] = 'up';

        return $formData;
    }
}
