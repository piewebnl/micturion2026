<?php

use Illuminate\Support\Facades\Schedule;

// Online stuff
/*
Schedule::command('command:ItunesLibraryImport')->hourly();
Schedule::command('command:AlbumImageCreate')->everySixHours();

// iTunes CSV
Schedule::command('command:ItunesCsvPlaylistFromSpotifyPlaylist')->daily();

// Wishlist
Schedule::command('command:WishlistAlbumPricesScrape')->everySixHours();
Schedule::command('command:WishlistAlbumPricesLocalToLive')->hourly();

// Discogs
Schedule::command('command:DiscogsCollectionImport')->everySixHours();
Schedule::command('command:DiscogsReleaseInfoImport')->everySixHours();

// Spines
Schedule::command('command:SpineImageExtractor')->everySixHours();
Schedule::command('command:SpineImageCreate')->everySixHours();

// Spotify
Schedule::command('command:SpotifyPlaylistsImport')->hourly();
Schedule::command('command:SpotifyPlaylistTracksImport')->hourly();
Schedule::command('command:SpotifyPlaylistTracksExport')->everySixHours();
Schedule::command('command:SpotifyTracksFavouriteExport')->everySixHours();
Schedule::command('command:SpotifyAlbumsExport')->everySixHours();
Schedule::command('command:SpotifySearchAndImportAlbums')->hourly();
Schedule::command('command:SpotifySearchAndImportAlbumTracks')->hourly();
*/

// Backups
Schedule::command('backup:clean')->daily()->at('06:00');
Schedule::command('backup:run')->daily()->at('07:00');
