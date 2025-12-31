<?php

namespace App\Http\Controllers\LastFmApi;

use App\Http\Controllers\Controller;

// Make a connection/session to the lastfm API via auth token
class LastFmApiAuthorizeController extends Controller
{
    public function index()
    {
        $lastFmApiKey = env('LAST_FM_API_KEY');

        return view('last-fm.last-fm-authorize', ['lastFmApiKey' => $lastFmApiKey]);
    }
}
