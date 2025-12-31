<?php

namespace App\Http\Controllers\Music;

use App\Http\Controllers\Controller;
use App\Livewire\Forms\Music\AlbumRandomSearchFormData;

class AlbumRandomController extends Controller
{
    public function index()
    {
        $albumRandomSearchFormData = new AlbumRandomSearchFormData;
        $searchFormData = $albumRandomSearchFormData->generate();

        return view('music.album-random', ['searchFormData' => $searchFormData]);
    }
}
