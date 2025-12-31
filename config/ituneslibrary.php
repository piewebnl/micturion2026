<?php

return [

    'itunes_library_xml_file' => env('ITUNES_LIBRARY_XML_FILE'),

    'itunes_library_extra_tracks_csv_file' => env('ITUNES_LIBRARY_EXTRA_TRACKS_CSV_FILE'),

    'ftp_itunes_library_xml_file' => env('FTP_ITUNES_LIBRARY_XML_FILE'),

    // (itunes) playlist to import to database (parent) persistent ids
    'itunes_playlists_to_import' => ['Best Of',  'Most Played Top 500', 'Top 50', 'Favourite Songs'],

    // itunes csv playlist from spotify playlist
    'itunes_csv_playlist_from_spotify_playlist' => ['Rutger Debbie'],

    // playlist 'favourite' is seperate process
    'itunes_tracks_favourite_playlist_persistent_id' => '2D1865B67512E211',

    'itunes_name_checker' => [
        'exception_words' => [
            '×',
            '\/',
            ' + ',
            "'n'",
            '(continued)',
            '(+',
            "'s",
            "'cept",
            "'bout)",
            "'bout",
            'tracklist',
            '(reprise)',
            '(from',
            "'n",
            '(-)',
            '(feat.',
            'feat.',
            '(with',
            '(met',
            'MCMLXXII',
        ],
        'extra_info' => [
            '(Live)',
            '(Alternative)',
            '(feat. ',
            '(Acoustic)',
            '(2 Meter Sessie)',
            '(BBC Session)',
        ],
        'artist' => [
            'System Of A Down',
            'E',
            'Sparks vs. Faith No More',
        ],
        'album' => [
            'R',
            'O',
            'I',
            'In•ter a•li•a',
            'CLOSURE / CONTINUATION',
            'MCMLXXII',
        ],
        'name' => [
            'I',
            '7',
            'bmbmbm',
            '.3',
            'In ár gCroíthe go deo',
            '2 X 4',
            "Mission From 'arry",
            'E',
            'I I E E E',
            'u, u, d, d, l, r, l, r, a, b, select, start',
            '5',
            'X',
            'M',
            '(sic)',
            'Trial Of Tears The End (?)',
            '°',
        ],

    ],
];
