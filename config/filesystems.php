<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

        // Public
        'images' => [
            'driver' => 'local',
            'root' => storage_path('app/public/images'),
            'url' => env('APP_URL') . '/images',
            'visibility' => 'public',
            'throw' => false,
        ],

        'spine_images_extracted' => [
            'driver' => 'local',
            'root' => storage_path('app/public/spine-images-extracted'),
            'url' => env('APP_URL') . '/spine-images-extracted',
            'visibility' => 'public',
        ],

        'discogs_back_artwork' => [
            'driver' => 'local',
            'root' => public_path('images/discogs-back-artwork'),
            'url' => env('APP_URL') . '/discogs-back-artwork',
            'visibility' => 'public',
        ],

        'playlists' => [
            'driver' => 'local',
            'root' => public_path('playlists'),
            'url' => env('APP_URL') . '/playlists',
            'visibility' => 'public',
        ],

        'ituneslibrary' => [
            'driver' => 'local',
            'root' => storage_path('app/ituneslibrary'),
        ],

        'ftp' => [
            'driver' => 'ftp',
            'host' => env('FTP_HOST'),
            'username' => env('FTP_USERNAME'),
            'password' => env('FTP_PASSWORD'),

            // Optional FTP Settings...
            // 'port' => env('FTP_PORT', 21),
            // 'root' => env('FTP_ROOT'),
            // 'passive' => true,
            // 'ssl' => true,
            // 'timeout' => 30,
        ],

        'hiby' => [
            'driver' => 'ftp',
            'host' => env('HIBY_FTP_HOST'),
            'port' => (int) env('HIBY_FTP_PORT', 2221),
            'username' => env('HIBY_FTP_USER'),
            'password' => env('HIBY_FTP_PASS'),
            'root' => env('HIBY_FTP_PATH'),
            'passive' => true,
            'ssl' => false,
            'timeout' => 60,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
