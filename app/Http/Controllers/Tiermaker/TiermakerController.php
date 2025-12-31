<?php

namespace App\Http\Controllers\Tiermaker;

use App\Http\Controllers\Controller;
use App\Livewire\Forms\Tiermaker\TiermakerSearchFormData;

class TiermakerController extends Controller
{
    public function index()
    {

        $tiermakerSearchFormData = new TiermakerSearchFormData;
        $searchFormData = $tiermakerSearchFormData->generate();

        return view('layouts.livewire-page', [
            'livewireComponents' => [
                [
                    'name' => 'tiermaker.tiermaker-search',
                    'searchFormData' => $searchFormData,
                ],
                [
                    'name' => 'tiermaker.tiermaker-results',
                    'searchFormData' => $searchFormData,
                ],
            ],
        ]);
    }

    public function edit(int $id)
    {

        return view('layouts.livewire-page', [
            'livewireComponents' => [
                [
                    'name' => 'tiermaker.tiermaker-edit',
                    'id' => $id,
                ],
            ],
        ]);
    }

    public function show(int $id)
    {
        return view('layouts.livewire-page', [
            'livewireComponents' => [
                [
                    'name' => 'tiermaker.tiermaker-show',
                    'id' => $id,
                ],
            ],
        ]);
    }
}
