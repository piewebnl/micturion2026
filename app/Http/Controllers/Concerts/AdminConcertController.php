<?php

namespace App\Http\Controllers\Concerts;

use App\Http\Controllers\Controller;

class AdminConcertController extends Controller
{
    public function index()
    {

        return view('layouts.livewire-page', [
            'livewireComponents' => [
                [
                    'name' => 'admin.concerts.admin-concerts',
                ],
            ],
        ]);
    }

    public function edit()
    {

        return view('layouts.livewire-page', [
            'livewireComponents' => [
                [
                    'name' => 'admin.concerts.admin-concert-edit',
                ],
            ],
        ]);
    }
}
