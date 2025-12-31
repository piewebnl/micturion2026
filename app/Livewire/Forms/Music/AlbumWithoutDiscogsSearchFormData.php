<?php

namespace App\Livewire\Forms\Music;

use App\Traits\QueryCache\QueryCache;

class AlbumWithoutDiscogsSearchFormData
{
    use QueryCache;

    public function generate(): array
    {

        $formData['matched'] = [
            ['value' => 'all', 'label' => 'All'],
            ['value' => 'skipped', 'label' => 'Skipped'],
            ['value' => 'not_skipped', 'label' => 'Not skipped'],
        ];

        return $formData;
    }
}
