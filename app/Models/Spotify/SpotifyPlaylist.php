<?php

namespace App\Models\Spotify;

use App\Scopes\GlobalScopesTrait;
use App\Scopes\SpotifyPlaylistScopesTrait;
use Illuminate\Database\Eloquent\Model;

// Spotify playlists that are stored in database
class SpotifyPlaylist extends Model
{
    use GlobalScopesTrait;
    use SpotifyPlaylistScopesTrait;

    protected $guarded = [];

    private $spotifyPlaylists;

    public function spotifyPlaylistTracks()
    {
        return $this->hasMany(SpotifyPlaylistTrack::class, 'spotify_playlist_id', 'id');
    }

    public function spotifyTracks()
    {
        return $this->hasManyThrough(SpotifyTrack::class, SpotifyPlaylistTrack::class, 'spotify_playlist_id', 'spotify_id', 'id', 'track_id');
    }

            public function getSpotifyPlaylistsWithTracks(array $filterValues)
    {
        return SpotifyPlaylist::selectRaw(
            'spotify_playlists.name as name,
                spotify_playlist_tracks.order as spotify_playlist_track_order,
                spotify_playlist_tracks.id as spotify_playlist_track_id,
                spotify_tracks.artist as spotify_playlist_track_artist,
                spotify_tracks.album as spotify_playlist_track_album,
                spotify_tracks.name as spotify_playlist_track_name,
                spotify_tracks.artwork_url as spotify_playlist_track_artwork_url'
        )
            ->join('spotify_playlist_tracks', 'spotify_playlist_tracks.spotify_playlist_id', '=', 'spotify_playlists.id')
            ->join('spotify_tracks', 'spotify_tracks.id', '=', 'spotify_playlist_tracks.spotify_track_id')
            ->spotifyPlaylistWhereYear($filterValues)
            ->spotifyPlaylistWhereKeyword($filterValues)
            ->spotifyPlaylistWhereName($filterValues)
            ->groupBy('spotify_playlist_tracks.id')
            ->spotifyPlaylistSortAndOrderBy($filterValues)
            ->customPaginateOrLimit($filterValues);
    }

        public function getSpotifyPlaylistByName(string $playlistName)
    {
        return SpotifyPlaylist::where('name', $playlistName)->first();
    }

    public function getSpotifyPlaylistsToImport()
    {
        $playlistsToImport = config('spotify.playlist_tracks_to_import_from_spotify');

        return SpotifyPlaylist::where(function ($query) use ($playlistsToImport) {
            foreach ($playlistsToImport as $name) {
                $query->orWhere('name', 'like', '%' . $name . '%');
            }
        })->get();
    }

    public function areSpotifyPlaylistsImported()
    {
        $spotifyPlaylists = SpotifyPlaylist::all();
        if ($spotifyPlaylists->isNotEmpty()) {
            return true;
        }
    }
}
