<?php

namespace App\Http\Controllers\Spotify;

use App\Http\Controllers\Controller;
use App\Livewire\Forms\Spotify\SpotifyReviewSearchFormData;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;

class SpotifyController extends Controller
{
    public function index()
    {
        $spotifyConnect = new SpotifyApiConnect;

        $spotifyConnect->getApi();
        $response = $spotifyConnect->getResponse();

        if ($response->getData()->status == 'success') {
            session()->flash('success', 'Spotify connection successfull!');

            $spotifyReviewSearchFormData = new SpotifyReviewSearchFormData;
            $searchFormData = $spotifyReviewSearchFormData->generate();

            return view('spotify.spotify', ['searchFormData' => $searchFormData]);
        } else {

            $spotifyConnect->getAuthorizeUrl();

            $authUrl = $spotifyConnect->getResponse()->original;

            return view('spotify.spotify-connect', ['authUrl' => $authUrl]);
        }
    }
}
