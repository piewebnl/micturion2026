<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

// php artisan command:Sync
class SyncCommand extends Command
{
    protected $signature = 'command:Sync';

    private string $channel;

    public function handle()
    {

        // iTunes
        $this->call('command:ItunesLibraryImport'); // Multiple commands inside
        // $this->call('command:ItunesLibraryChecker');
        $this->call('command:AlbumImageCreate');

        // To FTP [Local Only]
        $this->call('command:ItunesLibraryCopyXmlToFtp');
        $this->call('command:AlbumImageUploadToFtp');
        $this->call('command:AlbumImageOthersUploadToFtp');
        $this->call('command:BestOfArtworkImageUploadToFtp');

        // Backup online CSV [Local Only]
        $this->call('command:DatabaseDumper');

        // Images from production [Local Only]
        $this->call('command:ConcertImageDownloadFromFtp');
        $this->call('command:ConcertFestivalImageDownloadFromFtp');

        // Concert images [Local Only]
        $this->call('command:ConcertImageCreate');
        $this->call('command:ConcertFestivalImageCreate');

        // To iNas Plex Amp [Local Only]
        $this->call('command:MusicToPlexAmp');

        // To Hiby
        // $this->call('command:MusicToHiby');

        // Discogs
        $this->call('command:DiscogsCollectionImport');
        $this->call('command:DiscogsCollectionMatcher');
        $this->call('command:DiscogsReleaseInfoImport');

        // Spines
        $this->call('command:SpineImageExtractor');
        $this->call('command:SpineImageCreate');

        // Spotify

        $this->call('command:SpotifyPlaylistsImport');
        $this->call('command:SpotifyPlaylistTracksImport');
        $this->call('command:SpotifySearchAndImportAlbums');
        $this->call('command:SpotifyAlbumsExport');
        $this->call('command:SpotifySearchAndImportAlbumTracks');
    }
}
