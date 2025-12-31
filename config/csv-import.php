<?php

return [
    'concert_artist_import' => [
        'url' => 'https://micturion.com/api/concert-artists',
        'columns' => ['id', 'name'],
        'file' => './database/seeders/csvs/ConcertArtists.csv',
    ],

    'concert_venue_import' => [
        'url' => 'https://micturion.com/api/concert-venues',
        'columns' => ['id', 'name', 'city', 'country', 'festival'],
        'file' => './database/seeders/csvs/ConcertVenues.csv',
    ],

    'concert_festival_import' => [
        'url' => 'https://micturion.com/api/concert-festivals',
        'columns' => ['id', 'name'],
        'file' => './database/seeders/csvs/ConcertFestivals.csv',
    ],

    'concert_import' => [
        'url' => 'https://micturion.com/api/concerts',
        'columns' => ['id', 'date', 'concert_venue_id', 'concert_festival_id', 'notes'],
        'file' => './database/seeders/csvs/Concerts.csv',
    ],

    'concert_item_import' => [
        'url' => 'https://micturion.com/api/concert-items',
        'columns' => ['id', 'concert_id', 'concert_artist_id', 'support', 'setlistfm_url', 'image_url', 'order'],
        'file' => './database/seeders/csvs/ConcertItems.csv',
    ],

    'spotify_import_custom_track_ids' => [
        'url' => 'https://micturion.com/api/spotify/tracks/custom-ids',
        'columns' => ['persistent_id', 'spotify_api_track_custom_id', 'artist', 'album', 'name'],
        'file' => './database/seeders/csvs/SpotifyTrackCustomIds.csv',
    ],

    // tracks
    'spotify_import_tracks_unavailable_ids' => [
        'url' => 'https://micturion.com/api/spotify/tracks/unavailable',
        'columns' => ['persistent_id', 'artist', 'album', 'name'],
        'file' => './database/seeders/csvs/SpotifyTracksUnavailable.csv',
    ],

    'spotify_import_custom_album_ids' => [
        'url' => 'https://micturion.com/api/spotify/albums/custom-ids',
        'columns' => ['persistent_id', 'spotify_api_album_custom_id', 'artist', 'name'],
        'file' => './database/seeders/csvs/SpotifyAlbumCustomIds.csv',
    ],

    // albums
    'spotify_import_albums_unavailable_ids' => [
        'url' => 'https://micturion.com/api/spotify/albums/unavailable',
        'columns' => ['persistent_id', 'artist', 'name'],
        'file' => './database/seeders/csvs/SpotifyAlbumsUnavailable.csv',
    ],

    'music_stores_import' => [
        'url' => 'https://micturion.com/api/wishlist/music-stores',
        'columns' => ['id', 'key', 'name', 'url', 'scrape'],
        'file' => './database/seeders/csvs/MusicStores.csv',
    ],

    'wishlist_album_import' => [
        'url' => 'https://micturion.com/api/wishlist/wishlist-albums',
        'columns' => ['persistent_album_id', 'notes', 'format'],
        'file' => './database/seeders/csvs/WishlistAlbums.csv',
    ],

    'discogs_release_custom_id_import' => [
        'url' => 'https://micturion.com/api/discogs/release-custom-ids',
        'columns' => ['persistent_album_id', 'release_id', 'artist', 'title'],
        'file' => './database/seeders/csvs/DiscogsReleaseCustomIds.csv',
    ],

    'tiermaker_artists_import' => [
        'url' => 'https://micturion.com/api/tiermaker/artists',
        'columns' => ['id', 'artist_name'],
        'file' => './database/seeders/csvs/TiermakerArtists.csv',
    ],

    'tiermaker_albums_import' => [
        'url' => 'https://micturion.com/api/tiermaker/albums',
        'columns' => ['tiermaker_id', 'album_persistent_id', 'order', 'tier'],
        'file' => './database/seeders/csvs/TiermakerAlbums.csv',
    ],

];
