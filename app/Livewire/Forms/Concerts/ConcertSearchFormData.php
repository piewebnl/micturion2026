<?php

namespace App\Livewire\Forms\Concerts;

use App\Models\Concert\Concert;
use App\Models\Concert\ConcertArtist;
use App\Models\Concert\ConcertFestival;
use App\Models\Concert\ConcertVenue;
use App\Services\Forms\FormDataGenerator;
use App\Traits\QueryCache\QueryCache;

class ConcertSearchFormData
{
    use QueryCache;

    public function generate(): array
    {

        $formDataGenerator = new FormDataGenerator;

        // Years
        $concert = new Concert;
        $items = $concert->getAllConcertYears();
        $formDataGenerator->setLabel(['years']);
        $formDataGenerator->setValue('years');
        $formData['years'] = $formDataGenerator->generate($items);

        // Artists
        $concertArtist = new ConcertArtist;
        $items = $concertArtist->getAllConcertArtists();
        $formDataGenerator->setLabel(['name']);
        $formDataGenerator->setValue('id');
        $formData['names'] = $formDataGenerator->generate($items);

        // Venues
        $concertVenue = new ConcertVenue;
        $items = $concertVenue->getAllConcertVenues();
        $formDataGenerator->setLabel(['name']);
        $formDataGenerator->setValue('id');
        $formData['concert_venues'] = $formDataGenerator->generate($items);

        // Festivals
        $concertFestival = new ConcertFestival;
        $items = $concertFestival->getAllConcertFestivals();
        $formDataGenerator->setLabel(['name']);
        $formDataGenerator->setValue('id');
        $formData['concert_festivals'] = $formDataGenerator->generate($items);

        $formData['sort'] = [
            ['value' => 'date', 'label' => 'Date', 'order' => 'desc'],
        ];

        $formData['view'] = [
            ['value' => 'grid', 'label' => 'List', 'icon' => 'list'],
            ['value' => 'table', 'label' => 'Table', 'icon' => 'table'],
        ];

        $formData['order_toggle_icon'] = 'up';

        return $formData;
    }
}
