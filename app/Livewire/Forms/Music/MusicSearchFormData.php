<?php

namespace App\Livewire\Forms\Music;

use App\Models\Music\Album;
use App\Models\Music\Category;
use App\Models\Music\Format;
use App\Models\Music\Genre;
use App\Services\Forms\FormDataGenerator;
use App\Traits\QueryCache\QueryCache;

class MusicSearchFormData
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

        // Genres
        $genre = new Genre;
        $items = $genre->getAllGenres();
        $formData['genres'] = $formDataGenerator->generate($items);

        // Years
        $album = new Album;
        $items = $album->getAllYears();
        $formDataGenerator->setValue('year');
        $formDataGenerator->setLabel(['year']);
        $formData['years'] = $formDataGenerator->generate($items);

        $formData['sort'] = [
            ['value' => 'album_year', 'label' => 'Date', 'order' => 'desc'],
            ['value' => 'artist', 'label' => 'Name', 'order' => 'asc'],
            ['value' => 'album_play_count', 'label' => 'Album play count', 'order' => 'asc'],
        ];

        $formData['view'] = [
            ['value' => 'grid', 'label' => 'Grid', 'icon' => 'grid'],
            ['value' => 'list', 'label' => 'List', 'icon' => 'list'],
            ['value' => 'table', 'label' => 'Table', 'icon' => 'table'],
            ['value' => 'spines', 'label' => 'Spines', 'icon' => 'table', 'admin' => true],
        ];

        $formData['spine_images_checked'] = [
            ['value' => 1, 'label' => 'Checked'],
            ['value' => 0, 'label' => 'Non-checked'],
            ['value' => 'both', 'label' => 'Both'],
        ];

        $formData['order_toggle_icon'] = 'up';

        return $formData;
    }
}
