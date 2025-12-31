<?php

namespace App\Http\Controllers\Concerts;

use App\Http\Controllers\Controller;
use App\Livewire\Forms\Concerts\ConcertSearchFormData;

class ConcertController extends Controller
{
    public function index()
    {

        $concertSearchFormData = new ConcertSearchFormData;
        $searchFormData = $concertSearchFormData->generate();

        return view('layouts.livewire-page', [
            'livewireComponents' => [
                [
                    'name' => 'concerts.concert-search',
                    'searchFormData' => $searchFormData,
                ],
                [
                    'name' => 'concerts.concert-results',
                    'searchFormData' => $searchFormData,
                ],
            ],
        ]);
    }
}
