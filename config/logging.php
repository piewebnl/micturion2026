<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that is utilized to write
    | messages to your logs. The value provided here should match one of
    | the channels present in the list of "channels" configured below.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the log channel that should be used to log warnings
    | regarding deprecated PHP and library features. This allows you to get
    | your application ready for upcoming major versions of dependencies.
    |
    */

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => env('LOG_DEPRECATIONS_TRACE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Laravel
    | utilizes the Monolog PHP logging library, which includes a variety
    | of powerful log handlers and formatters that you're free to use.
    |
    | Available drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog", "custom", "stack"
    |
    */

    'channels' => [

        'stack' => [
            'driver' => 'stack',
            'channels' => explode(',', (string) env('LOG_STACK', 'single')),
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => env('LOG_DAILY_DAYS', 14),
            'replace_placeholders' => true,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => env('LOG_SLACK_USERNAME', 'Laravel Log'),
            'emoji' => env('LOG_SLACK_EMOJI', ':boom:'),
            'level' => env('LOG_LEVEL', 'critical'),
            'replace_placeholders' => true,
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => env('LOG_PAPERTRAIL_HANDLER', SyslogUdpHandler::class),
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
                'connectionString' => 'tls://' . env('PAPERTRAIL_URL') . ':' . env('PAPERTRAIL_PORT'),
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'handler_with' => [
                'stream' => 'php://stderr',
            ],
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
            'facility' => env('LOG_SYSLOG_FACILITY', LOG_USER),
            'replace_placeholders' => true,
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],
        // Custom
        'config' => [
            'driver' => 'single',
            'path' => storage_path('logs/config.log'),
            'days' => 1,
        ],

        'menu_create_images' => [
            'driver' => 'single',
            'path' => storage_path('logs/menu_create_images.log'),
            'days' => 1,
        ],

        'menu_create_images' => [
            'driver' => 'single',
            'path' => storage_path('logs/menu_create_images.log'),
            'days' => 1,
        ],

        'energy_prices' => [
            'driver' => 'single',
            'path' => storage_path('logs/energy_prices.log'),
            'days' => 1,
        ],

        'concert_create_images' => [
            'driver' => 'single',
            'path' => storage_path('logs/concert_create_images.log'),
            'days' => 1,
        ],

        'concert_festival_create_images' => [
            'driver' => 'single',
            'path' => storage_path('logs/concert_festival_create_images.log'),
            'days' => 1,
        ],

        'album_create_images' => [
            'driver' => 'single',
            'path' => storage_path('logs/album_create_images.log'),
            'days' => 1,
        ],

        'spine_image_extractor' => [
            'driver' => 'single',
            'path' => storage_path('logs/spine_image_extractor.log'),
            'days' => 1,
        ],

        'spine_image_create_images' => [
            'driver' => 'single',
            'path' => storage_path('logs/spine_image_create_images.log'),
            'days' => 1,
        ],

        'album_image_upload_to_ftp' => [
            'driver' => 'single',
            'path' => storage_path('logs/album_image_upload_to_ftp.log'),
            'days' => 1,
        ],

        'album_image_others_upload_to_ftp' => [
            'driver' => 'single',
            'path' => storage_path('logs/album_image_others_upload_to_ftp.log'),
            'days' => 1,
        ],

        'best_of_artwork_image_upload_to_ftp' => [
            'driver' => 'single',
            'path' => storage_path('logs/best_of_artwork_image_upload_to_ftp.log'),
            'days' => 1,
        ],

        'music_to_plex_amp' => [
            'driver' => 'single',
            'path' => storage_path('logs/music_to_plex_amp.log'),
            'days' => 1,
        ],

        'music_to_hiby' => [
            'driver' => 'single',
            'path' => storage_path('logs/music_to_hiby.log'),
            'days' => 1,
        ],

        'itunes_library_xml_upload_to_ftp' => [
            'driver' => 'single',
            'path' => storage_path('logs/itunes_library_xml_upload_to_ftp.log'),
            'days' => 1,
        ],

        'itunes_library_importer' => [
            'driver' => 'single',
            'path' => storage_path('logs/itunes_library_importer.log'),
            'days' => 1,
        ],

        'itunes_library_import_playlists' => [
            'driver' => 'single',
            'path' => storage_path('logs/itunes_library_import_playlists.log'),
            'days' => 1,
        ],

        'itunes_library_import_playlist_tracks' => [
            'driver' => 'single',
            'path' => storage_path('logs/itunes_library_import_playlist_tracks.log'),
            'days' => 1,
        ],

        'itunes_library_import_tracks' => [
            'driver' => 'single',
            'path' => storage_path('logs/itunes_library_import_tracks.log'),
            'days' => 1,
        ],

        'itunes_library_import_extra_tracks' => [
            'driver' => 'single',
            'path' => storage_path('logs/itunes_library_import_extra_tracks.log'),
            'days' => 1,
        ],

        'itunes_library_import_tracks_favourite' => [
            'driver' => 'single',
            'path' => storage_path('logs/itunes_library_import_tracks_favourite.log'),
            'days' => 1,
        ],

        'itunes_library_import_last_fm_play_counts' => [
            'driver' => 'single',
            'path' => storage_path('logs/itunes_library_import_last_fm_play_counts.log'),
            'days' => 1,
        ],

        'itunes_library_checker' => [
            'driver' => 'single',
            'path' => storage_path('logs/itunes_library_checker.log'),
            'days' => 1,
        ],

        'concert_venue_import_csv' => [
            'driver' => 'single',
            'path' => storage_path('logs/concert_venue_import_csv.log'),
            'days' => 1,
        ],

        'concert_artist_import_csv' => [
            'driver' => 'single',
            'path' => storage_path('logs/concert_artist_import_csv.log'),
            'days' => 1,
        ],

        'concert_festival_import_csv' => [
            'driver' => 'single',
            'path' => storage_path('logs/concert_festival_import_csv.log'),
            'days' => 1,
        ],
        'concert_item_import_csv' => [
            'driver' => 'single',
            'path' => storage_path('logs/concert_item_import_csv.log'),
            'days' => 1,
        ],
        'concert_import_csv' => [
            'driver' => 'single',
            'path' => storage_path('logs/concert_import_csv.log'),
            'days' => 1,
        ],
        'concert_image_download_from_ftp' => [

            'driver' => 'single',
            'path' => storage_path('logs/concert_image_download_from_ftp.log'),
            'days' => 1,
        ],
        'concert_festival_image_download_from_ftp' => [

            'driver' => 'single',
            'path' => storage_path('logs/concert_festival_image_download_from_ftp.log'),
            'days' => 1,
        ],
        'spotify_api_connect' => [
            'driver' => 'single',
            'path' => storage_path('logs/spotify_api_connect.log'),
            'days' => 1,
        ],

        'spotify_import' => [
            'driver' => 'single',
            'path' => storage_path('logs/spotify_import.log'),
            'days' => 1,
        ],
        'spotify_playlist_tracks_import' => [
            'driver' => 'single',
            'path' => storage_path('logs/spotify_playlist_tracks_import.log'),
            'days' => 1,
        ],
        'spotify_playlists_import' => [
            'driver' => 'single',
            'path' => storage_path('logs/spotify_playlists_import.log'),
            'days' => 1,
        ],
        'spotify_search_and_import_tracks' => [
            'driver' => 'single',
            'path' => storage_path('logs/spotify_search_and_import_tracks.log'),
            'days' => 1,
        ],
        'spotify_import_csv_custom_track_ids' => [
            'driver' => 'single',
            'path' => storage_path('logs/spotify_import_csv_custom_track_ids.log'),
            'days' => 1,
        ],
        'spotify_import_tracks_unavailable' => [
            'driver' => 'single',
            'path' => storage_path('logs/spotify_import_tracks_unavailable.log'),
            'days' => 1,
        ],
        'spotify_search_and_import_albums' => [
            'driver' => 'single',
            'path' => storage_path('logs/spotify_search_and_import_albums.log'),
            'days' => 1,
        ],
        'spotify_import_csv_custom_album_ids' => [
            'driver' => 'single',
            'path' => storage_path('logs/spotify_import_csv_custom_album_ids.log'),
            'days' => 1,
        ],
        'spotify_import_albums_unavailable' => [
            'driver' => 'single',
            'path' => storage_path('logs/spotify_import_albums_unavailable.log'),
            'days' => 1,
        ],
        'spotify_export' => [
            'driver' => 'single',
            'path' => storage_path('logs/spotify_export.log'),
            'days' => 1,
        ],
        'spotify_playlists_tracks_export' => [
            'driver' => 'single',
            'path' => storage_path('logs/spotify_playlists_tracks_export.log'),
            'days' => 1,
        ],
        'spotify_tracks_favourite_export' => [
            'driver' => 'single',
            'path' => storage_path('logs/spotify_tracks_favourite_export.log'),
            'days' => 1,
        ],
        'spotify_albums_export' => [
            'driver' => 'single',
            'path' => storage_path('logs/spotify_albums_export.log'),
            'days' => 1,
        ],
        'wishlist_albums_scrape_prices' => [
            'driver' => 'single',
            'path' => storage_path('logs/wishlist_albums_scrape_prices.log'),
            'days' => 1,
        ],
        'wishlist_album_import_csv' => [
            'driver' => 'single',
            'path' => storage_path('logs/wishlist_album_import_csv.log'),
            'days' => 1,
        ],

        'wishlist_albums_prices_local_to_live' => [
            'driver' => 'single',
            'path' => storage_path('logs/wishlist_albums_prices_local_to_live.log'),
            'days' => 1,
        ],

        'wishlist_albums_live_to_local' => [
            'driver' => 'single',
            'path' => storage_path('logs/wishlist_albums_live_to_local.log'),
            'days' => 1,
        ],

        'html_scraper' => [
            'driver' => 'single',
            'path' => storage_path('logs/html_scraper.log'),
            'days' => 1,
        ],

        'discogs_collection_importer' => [
            'driver' => 'single',
            'path' => storage_path('logs/discogs_collection_importer.log'),
            'days' => 1,
        ],

        'discogs_release_info_importer' => [
            'driver' => 'single',
            'path' => storage_path('logs/discogs_release_info_importer.log'),
            'days' => 1,
        ],

        'discogs_collection_matcher' => [
            'driver' => 'single',
            'path' => storage_path('logs/discogs_collection_matcher.log'),
            'days' => 1,
        ],
    ],

];
