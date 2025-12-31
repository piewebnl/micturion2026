<?php

namespace App\Http\Controllers\Music;

use App\Http\Controllers\Controller;
use App\Livewire\Forms\Music\AlbumWithoutDiscogsSearchFormData;

class AlbumWithoutDiscogsController extends Controller
{
    public function index()
    {
        $albumWithoutDiscogsSearchFormData = new AlbumWithoutDiscogsSearchFormData;
        $searchFormData = $albumWithoutDiscogsSearchFormData->generate();

        return view('layouts.livewire-page', [
            'livewireComponents' => [
                [
                    'name' => 'music.album-without-discogs-search',
                    'searchFormData' => $searchFormData,
                ],
                [
                    'name' => 'music.album-without-discogs-results',
                    'searchFormData' => $searchFormData,
                ],
            ],
        ]);
    }
}
