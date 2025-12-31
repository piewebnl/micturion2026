<?php

namespace App\Http\Controllers\LastFmApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\LastFmApi\LastFmScrobbleTrackRequest;
use App\Models\Music\Song;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use LastFmApi\Api\AuthApi;
use LastFmApi\Api\TrackApi;

class LastFmScrobbleTrackController extends Controller
{
    public function store(LastFmScrobbleTrackRequest $request): JsonResponse
    {
        $song = Song::where('id', $request->id)->with('artist', 'album')->first();

        if (!$song) {
            return response()->error('No song found');
        }

        $auth = new AuthApi('setsession', [
            'apiKey' => config('lastfm.last_fm_api_key'),
            'sessionKey' => Setting::getSetting('last_fm_session_key'),
            'apiSecret' => config('lastfm.last_fm_shared_secret'),
            'username' => 'micturion',
            'subscriber' => 0,

        ]);

        if (!$auth->sessionKey) {
            session()->flash('error', 'No valid Last FM connection');

            return response()->error('Not authorized LastFM');
        }

        try {
            $trackApi = new TrackApi($auth);
            $artist = $song->artist['name'];
            $album = $song->album['name'];
            $useArtist = $artist;
            if ($song->album_artist != '') {
                $useArtist = $song->album_artist;
            }

            $trackApi->scrobble(['artist' => $useArtist, 'track' => $song->name, 'album' => $album, 'timestamp' => Carbon::now()->timestamp]);
        } catch (\Exception $e) {
            // return response()->error('Last FM not scrobbled: ' . $e->getMessage());
        }

        return response()->success('Scrobbled ' . $artist . ' ' . $album . ' ' . $song->name);
    }
}
