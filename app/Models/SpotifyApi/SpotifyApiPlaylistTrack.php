<?php

namespace App\Models\SpotifyApi;

use App\Models\Spotify\SpotifyPlaylist;
use App\Models\Spotify\SpotifyPlaylistTrack;
use App\Models\Spotify\SpotifyTrack;
use App\Traits\Converters\ToSpotifyPlaylistTrackConverter;
use App\Traits\Converters\ToSpotifyTrackConverter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

// Spotify playlist tracks coming from spotify, stored via SpotifyPlaylistTrack
class SpotifyApiPlaylistTrack extends Model
{
    use ToSpotifyPlaylistTrackConverter;
    use ToSpotifyTrackConverter;

    private $resource = [];

    public function updateOrCreateSingle(SpotifyPlaylist $spotifyPlaylist, object $spotifyApiPlaylistTrack, int $order)
    {
        // Generate fake ID (for tracks not on spotify or from local library)
        if ($spotifyApiPlaylistTrack->id == null) {
            $spotifyApiPlaylistTrack->id = $this->generateFakeSpotifyTrackId($spotifyApiPlaylistTrack);
            $spotifyApiPlaylistTrack->is_found = false;
        }

        // Store the found spotify track
        $convertedSpotifyTrack = $this->convertSpotifyApiPlaylistTrackToSpotifyTrack($spotifyApiPlaylistTrack);
        $spotifyTrack = SpotifyTrack::updateOrCreate(
            ['spotify_api_track_id' => $convertedSpotifyTrack['spotify_api_track_id']],
            $convertedSpotifyTrack
        );

        // Store relation
        $convertedSpotifyPlaylistTrack = $this->convertSpotifyApiPlaylistToSpotifyPlaylistTrack($spotifyApiPlaylistTrack, $spotifyTrack, $spotifyPlaylist, $order);

        SpotifyPlaylistTrack::updateOrCreate($convertedSpotifyPlaylistTrack);

        $this->resource[] = [
            'status' => 'success',
            'ok' => true,
            'text' => 'Spotify playlist track stored',
            'name' => $spotifyApiPlaylistTrack->name,
            'artist' => $spotifyApiPlaylistTrack->artists[0]->name,
            'spotify_track' => $spotifyTrack,
            'playlist_name' => $spotifyPlaylist->name,
        ];
    }

    public function updateOrCreateAll(SpotifyPlaylist $spotifyPlaylist, array $spotifyApiPlaylistTracks)
    {
        foreach ($spotifyApiPlaylistTracks as $key => $spotifyApiPlaylistTrack) {
            $this->updateOrCreateSingle($spotifyPlaylist, $spotifyApiPlaylistTrack, $key);
        }
    }

    private function generateFakeSpotifyTrackId($spotifyApiPlaylistTrack)
    {
        $value = $spotifyApiPlaylistTrack->artists[0]->name . $spotifyApiPlaylistTrack->album->name . $spotifyApiPlaylistTrack->name;

        return 'NOTEXIST' . Str::limit(md5($value), 14, '');
    }

    public function getResource(): array
    {
        return $this->resource;
    }
}
