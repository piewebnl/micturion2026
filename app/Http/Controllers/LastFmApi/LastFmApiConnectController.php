<?php

namespace App\Http\Controllers\LastFmApi;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use LastFmApi\Api\AuthApi;

// Check if connection exist (session valid)
class LastFmApiConnectController extends Controller
{
    public function index()
    {

        $auth = new AuthApi('setsession', [
            'apiKey' => config('lastfm.last_fm_api_key'),
            'sessionKey' => Setting::getSetting('last_fm_session_key'),
            'apiSecret' => config('lastfm.last_fm_shared_secret'),
            'username' => 'micturion',
            'subscriber' => 0,

        ]);

        $lastFmApiKey = env('LAST_FM_API_KEY');

        return view('last-fm.last-fm-connect', ['lastFmApiKey' => $lastFmApiKey]);
    }
}
