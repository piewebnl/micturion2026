<?php

namespace App\Livewire\Forms\Discogs;

use App\Models\Music\Format;
use App\Services\Forms\FormDataGenerator;
use App\Traits\QueryCache\QueryCache;

class DiscogsSearchFormData
{
    use QueryCache;

    public function generate(): array
    {
        $formDataGenerator = new FormDataGenerator;

        // Formats
        $format = new Format;
        $items = $format->getAllFormats();
        $formData['formats'] = $formDataGenerator->generate($items);

        $formData['matched'] = [
            ['value' => 'all', 'label' => 'All'],
            ['value' => 'matched', 'label' => 'Matched'],
            ['value' => 'not_matched', 'label' => 'Not matched'],
            ['value' => 'skipped', 'label' => 'Skipped'],
        ];

        return $formData;
    }
}
