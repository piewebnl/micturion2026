<?php

namespace App\Livewire\Forms\Music;

use App\Models\Music\Artist;
use App\Models\Music\Category;
use App\Services\Forms\FormDataGenerator;
use App\Traits\QueryCache\QueryCache;

class AlbumPurchaseSearchFormData
{
    use QueryCache;

    public function generate(): array
    {
        $formDataGenerator = new FormDataGenerator;

        $category = new Category;
        $catIds = $category->getCategoriesByName(['Albums', 'EPs']);
        $filterValues['categories'] = $catIds;

        $artist = new Artist;
        $items = $artist->getArtistsWithAlbums($filterValues);

        // Fileament test
        $formDataGenerator->setLabel(['name', 'album_name']);
        $formDataGenerator->setValue('album_id');
        $formData['albums_filament'] = $formDataGenerator->generateFilament($items);

        return $formData;
    }
}
