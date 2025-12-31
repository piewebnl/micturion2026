<?php

namespace App\Http\Controllers\LastFm;

use App\Http\Controllers\Controller;
use App\Livewire\Forms\LastFm\LastFmSearchFormData;
use App\Models\Setting;
use LastFmApi\Api\AuthApi;

class LastFmScrobbleController extends Controller
{
    public function index()
    {
        $lastFmSearchFormData = new LastFmSearchFormData;
        $searchFormData = $lastFmSearchFormData->generate();

        $auth = new AuthApi('setsession', [
            'apiKey' => config('lastfm.last_fm_api_key'),
            'sessionKey' => Setting::getSetting('last_fm_session_key'),
            'apiSecret' => config('lastfm.last_fm_shared_secret'),
            'username' => 'micturion',
            'subscriber' => 0,
        ]);

        if (!$auth->sessionKey) {
            session()->flash('error', 'No valid Last FM connection');
            $lastFmApiKey = env('LAST_FM_API_KEY');

            return view('last-fm.last-fm-authorize', ['lastFmApiKey' => $lastFmApiKey]);
        }

        return view('layouts.livewire-page', [
            'livewireComponents' => [
                [
                    'name' => 'last-fm.last-fm-search',
                    'searchFormData' => $searchFormData,
                ],
                [
                    'name' => 'last-fm.last-fm-results',
                    'searchFormData' => $searchFormData,
                ],
            ],
        ]);
    }
}
