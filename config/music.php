<?php

return [

    'music_path' => env('MUSIC_PATH'),

    'ftp_music_path' => env('FTP_MUSIC_PATH'),

    'album_artwork_others_path' => env('ALBUM_ARTWORK_OTHERS_PATH'),

    'ftp_album_artwork_other_path' => env('FTP_ALBUM_ARTWORK_OTHERS_PATH'),

    'music_lost_files_path' => env('MUSIC_LOST_FILES_PATH'),

    'playlist_best_of_artwork_images_path' => env('PLAYLIST_BEST_OF_ARTWORK_IMAGES_PATH'),

    'ftp_playlist_best_of_artwork_images_path' => env('FTP_PLAYLIST_BEST_OF_ARTWORK_IMAGES_PATH'),

    'spine_images_path' => env('SPINE_IMAGES_PATH'),

    'plex_amp_path' => env('PLEX_AMP_PATH'),

    'wishlist_albums_local_csv' => env('WISHLIST_ALBUMS_LOCAL_CSV'),

    'wishlist_album_prices_local_csv' => env('WISHLIST_ALBUM_PRICES_LOCAL_CSV'),

    'ftp_wishlist_album_prices_local_csv' => env('FTP_WISHLIST_ALBUM_PRICES_LOCAL_CSV'),

    // Sanitization versions against the db version
    'name_helper_last_fm_to_itunes' => [
        'song' => [
            '★' => 'Blackstar',
            'King for a Day, Fool for a Lifetime' => 'King For A Day',
        ],
        'album' => [],
        'artist' => [
            'Angus & Julia Stone' => 'Angus and Julia Stone',
            '7Zuma7' => '7 Zuma 7',
            'Florance + The Machine' => 'Florence And The Machine',
        ],
    ],

];
