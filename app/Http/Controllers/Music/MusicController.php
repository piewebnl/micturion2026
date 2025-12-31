<?php

namespace App\Http\Controllers\Music;

use App\Http\Controllers\Controller;
use App\Livewire\Forms\Music\MusicSearchFormData;
use App\Models\Music\Artist;

class MusicController extends Controller
{
    public function index()
    {
        $musicSearchFormData = new MusicSearchFormData;
        $searchFormData = $musicSearchFormData->generate();

        return view('layouts.livewire-page', [
            'livewireComponents' => [
                [
                    'name' => 'music.music-search',
                    'searchFormData' => $searchFormData,
                ],
                [
                    'name' => 'music.music-results',
                    'searchFormData' => $searchFormData,
                ],
            ],
        ]);
    }

    public function show(int $id)
    {
        $music = Artist::with('venue')->findOrFail($id);

        return view('music.show', ['music' => $music]);
    }

    public function create()
    {
        $music = new Artist;

        return view('music.music-create', ['music' => $music]);
    }

    public function edit(int $id)
    {
        $music = Artist::with('venue')->findOrFail($id);

        return view('music.music-edit', ['music' => $music, 'id' => $id]);
    }
}
