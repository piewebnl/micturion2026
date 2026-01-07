<?php

namespace App\Livewire\Spotify;

use App\Livewire\Forms\Spotify\SpotifyReviewSearchFormInit;
use App\Models\Music\Album;
use App\Models\Music\Song;
use App\Models\Spotify\SpotifyAlbum;
use App\Models\Spotify\SpotifyTrack;
use App\Services\Spotify\Changers\SpotifyAlbumStatusChanger;
use App\Services\Spotify\Importers\SpotifyAlbumImporter;
use App\Services\Spotify\Importers\SpotifyTrackImporter;
use App\Services\SpotifyApi\Connect\SpotifyApiConnect;
use App\Services\SpotifyTrack\SpotifyTrackStatusChanger;
use App\Traits\QueryCache\QueryCache;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class SpotifyReviewResults extends Component
{
    private SpotifyReviewSearchFormInit $searchFormInit;

    use QueryCache;

    #[Url(as: 's', keep: true, history: true)]
    public $filterValues = [];

    public $searchFormData;

    private $perPage = 50;

    public function boot()
    {
        $this->searchFormInit = new SpotifyReviewSearchFormInit($this->searchFormData);
        $this->filterValues = request('s', $this->filterValues);
        $this->filterValues = $this->searchFormInit->init($this->filterValues);
    }

    #[On('spotify-review-results-change-track-status')]
    public function changeTrackStatus($spotifyTrackId, string $status)
    {
        $songSpotifyTrack = SpotifyTrack::find($spotifyTrackId);

        $songSpotifyTrackStatusChanger = new SpotifyTrackStatusChanger;
        $songSpotifyTrackStatusChanger->changeStatus($songSpotifyTrack, $status);

        $this->render();
    }

    #[On('spotify-review-results-change-album-status')]
    public function changeAlbumStatus($spotifyAlbumId, string $status)
    {
        $spotifyAlbum = SpotifyAlbum::find($spotifyAlbumId);

        $songSpotifyTrackStatusChanger = new SpotifyAlbumStatusChanger;
        $songSpotifyTrackStatusChanger->changeStatus($spotifyAlbum, $status);

        $this->render();
    }

    #[On('spotify-review-results-save-track-custom-id-url')]
    public function saveCustomTrackIdUrl($spotifyTrackId, $songId)
    {

        $song = Song::with('album.artist')->find($songId);

        $api = (new SpotifyApiConnect)->getApi();
        $spotifyTrackImporter = new SpotifyTrackImporter($api);
        $spotifyTrackImporter->import($spotifyTrackId, $song);

        $this->render();
    }

    #[On('spotify-review-results-save-album-custom-id-url')]
    public function saveCustomAlbumIdUrl($spotifyAlbumId, $albumId)
    {

        $album = Album::with('artist')->find($albumId);

        $api = (new SpotifyApiConnect)->getApi();
        $spotifyAlbumImporter = new SpotifyAlbumImporter($api);
        $spotifyAlbumImporter->import($spotifyAlbumId, $album);

        $this->render();
    }

    #[On('spotify-review-results-delete-track')]
    public function deleteTrack($spotifyTrackId)
    {

        $songSpotifyTrack = SpotifyTrack::find($spotifyTrackId);

        if ($songSpotifyTrack) {
            $songSpotifyTrack->delete();
        }
        $this->render();
    }

    #[On('spotify-review-searched')]
    public function searched($filterValues)
    {
        $this->filterValues = $filterValues;
    }

    public function render()
    {

        if ($this->filterValues['view'] == 'track') {
            $songSpotifyTrack = new SpotifyTrack;
            $tracks = $songSpotifyTrack->getSpotifyTracksWithSong($this->filterValues);

            return view('livewire.spotify.spotify-review-results-tracks', compact('tracks'));
        }

        if ($this->filterValues['view'] == 'album') {
            $spotifyAlbum = new SpotifyAlbum;
            $albums = $spotifyAlbum->getSpotifyAlbumWithAlbum($this->filterValues);

            return view('livewire.spotify.spotify-review-results-albums', compact('albums'));
        }
    }
}
