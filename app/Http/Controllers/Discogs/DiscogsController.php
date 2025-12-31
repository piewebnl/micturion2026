<?php

namespace App\Http\Controllers\Discogs;

use App\Http\Controllers\Controller;
use App\Livewire\Forms\Discogs\DiscogsSearchFormData;

class DiscogsController extends Controller
{
    public function index()
    {
        $discogsSearchFormData = new DiscogsSearchFormData;
        $searchFormData = $discogsSearchFormData->generate();

        return view('layouts.livewire-page', [
            'livewireComponents' => [
                [
                    'name' => 'discogs.discogs-search',
                    'searchFormData' => $searchFormData,
                ],
                [
                    'name' => 'discogs.discogs-results',
                    'searchFormData' => $searchFormData,
                ],
            ],
        ]);
    }
}
