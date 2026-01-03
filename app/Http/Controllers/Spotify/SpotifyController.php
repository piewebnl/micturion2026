<?php

namespace App\Http\Controllers\Spotify;

use App\Http\Controllers\Controller;
use App\Livewire\Forms\Spotify\SpotifyReviewSearchFormData;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;

class SpotifyController extends Controller
{
    public function index()
    {
        $spotifyConnect = new SpotifyApiConnect();

        $api = $spotifyConnect->getApi();


        if ($api) {
            session()->flash('success', 'Spotify connection successfull!');

            $spotifyReviewSearchFormData = new SpotifyReviewSearchFormData;
            $searchFormData = $spotifyReviewSearchFormData->generate();

            return view('spotify.spotify', ['searchFormData' => $searchFormData]);
        } else {

            $authUrl = $spotifyConnect->getAuthorizeUrl();
            return view('spotify.spotify-connect', ['authUrl' => $authUrl]);
        }
    }
}
