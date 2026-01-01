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

        // iTunes CSV
        $this->call('command:ItunesCsvPlaylistFromSpotifyPlaylist');

        // To FTP [Local Only]
        $this->call('command:ItunesLibraryCopyXmlToFtp');
        $this->call('command:AlbumImageCopyToFtp');
        $this->call('command:AlbumImageOthersCopyToFtp');
        $this->call('command:BestOfArtworkImageCopyToFtp');

        // Backup online CSV [Local Only]
        $this->call('command:DatabaseDumper');

        // Sync local DB with online CSV [Local Only]
        $this->call('command:WishlistAlbumLocalDbSync');

        // Images from production [Local Only]
        $this->call('command:ConcertImageCopyFromFtp');
        $this->call('command:ConcertFestivalImageCopyFromFtp');

        // Concert images [Local Only]
        $this->call('command:ConcertImageCreate');
        $this->call('command:ConcertFestivalImageCreate');

        // Wishlist Prices [Local Only]
        // $this->call('command:WishlistAlbumLocalDbSync');
        // $this->call('command:WishlistAlbumPricesLocalToLive');

        // Wishlist Prices
        // $this->call('command:WishlistAlbumPricesScrape');

        // To iNas Plex Amp [Local Only]
        //$this->call('command:MusicToPlexAmp');

        // To Hiby
        // $this->call('command:MusicToHiby');

        // Discogs
        $this->call('command:DiscogsCollectionImport');
        $this->call('command:DiscogsReleaseInfoImport');

        // Spines
        $this->call('command:SpineImageExtractor');
        $this->call('command:SpineImageCreate');

        // Spotify
        $this->call('command:SpotifyPlaylistsImport');
        $this->call('command:SpotifyPlaylistTracksImport');
        $this->call('command:SpotifyPlaylistTracksExport');
        $this->call('command:SpotifyTracksFavouriteExport');
        $this->call('command:SpotifyAlbumsExport');
        $this->call('command:SpotifyPlaylistTrackSongSearch');
        $this->call('command:SpotifySearchAndImportAlbums');
        $this->call('command:SpotifySearchAndImportTracks');
    }
}
