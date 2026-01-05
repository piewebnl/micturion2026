<?php

return [

    'spotify_user_id' => env('SPOTIFY_USER_ID'),
    'spotify_client_id' => env('SPOTIFY_CLIENT_ID'),
    'spotify_client_secret' => env('SPOTIFY_CLIENT_SECRET'),
    'spotify_callback' => env('SPOTIFY_CALLBACK'),

    // Max score via search = 100%, custom or manual upped = 150%
    'track_search_results_success_score' => 70,
    'track_search_results_warning_score' => 60,


    'album_search_results_success_score' => 60,
    'album_search_results_warning_score' => 30,

    'playlists_to_export_to_spotify' => ['Best Of', 'Favourites'], // name or parent name

    'playlist_tracks_to_import_from_spotify' => ['Playlist 20%', 'Rutger Debbie'], // Just the montlty

    'playlist_tracks_to_search_for_songs' => ['Rutger Debbie'],

];
