<?php

namespace App\Livewire\Forms\Music;

use App\Models\Music\Category;
use App\Models\Music\Format;
use App\Services\Forms\FormDataGenerator;
use App\Traits\QueryCache\QueryCache;

class AlbumRandomSearchFormData
{
    use QueryCache;

    public function generate(): array
    {
        $formDataGenerator = new FormDataGenerator;

        // Formats
        $format = new Format;
        $items = $format->getAllFormats();
        $formData['formats'] = $formDataGenerator->generate($items);

        // Categories
        $format = new Category;
        $items = $format->getAllCategories();
        $formData['categories'] = $formDataGenerator->generate($items);

        $formData['view'] = 'grid';

        return $formData;
    }
}
