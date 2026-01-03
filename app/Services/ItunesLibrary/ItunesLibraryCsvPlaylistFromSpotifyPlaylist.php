<?php

namespace App\Services\ItunesLibrary;

use App\Models\Spotify\SpotifyPlaylist;
use App\Services\Logger\Logger;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ItunesLibraryCsvPlaylistFromSpotifyPlaylist
{
    private $resource;

    public function makeCsvPlaylist($spotifyPlaylist)
    {

        $playlist = SpotifyPlaylist::with([
            'spotifyPlaylistTracks' => function ($q) {
                $q->whereHas('spotifyTrack.song');
            },
            'spotifyPlaylistTracks.spotifyTrack.song.album.artist',
        ])->find($spotifyPlaylist->id);

        if (!$playlist) {
            return;
        }

        $lines = [];
        $lines[] = '#EXTM3U';
        $lines[] = '#PLAYLIST:' . $playlist->name;

        $skipped = 0;
        foreach ($playlist->spotifyPlaylistTracks as $spt) {
            $song = optional($spt->spotifyTrack)->song;

            if (!$song) {
                $skipped++;

                continue;
            }

            $location = '/Volumes/iTunes/Music' . $this->normalizePath($song->location ?? '');
            if (empty($location)) {
                $skipped++;

                continue;
            }

            $artist = $this->sanitizeText(optional(optional($song->album)->artist)->name ?? 'Unknown Artist');
            $title = $this->sanitizeText($song->name ?? 'Untitled');
            $duration = $this->normalizeDuration($song->time ?? null); // seconds, -1 if unknown

            $lines[] = sprintf('#EXTINF:%d,%s - %s', $duration, $artist, $title);
            $lines[] = $location;
        }

        $safeName = $playlist->name . '.m3u8';

        $dir = 'playlists';
        Storage::disk('public')->makeDirectory($dir);
        $path = $dir . '/' . $safeName;

        Storage::disk('public')->put($path, implode("\n", $lines));

        $msg = sprintf(
            'Saved %s (%d tracks, %d skipped)',
            'public/' . $path,
            (count($lines) - 2) / 2, // two lines per track after headers
            $skipped
        );
        // $this->info($msg);
        // Logger::log('info', $this->channel, $msg);
    }

    private function sanitizeText(string $value): string
    {
        // M3U comments are tolerant, but strip newlines/tabs
        $value = str_replace(["\r", "\n", "\t"], ' ', $value);

        return trim($value);
    }

    private function normalizeDuration($time): int
    {
        if (empty($time)) {
            return -1;
        }

        // numeric (seconds or ms)
        if (is_numeric($time)) {
            $num = (int) round($time);

            return $num > 3600 * 100 ? (int) round($num / 1000) : $num;
        }

        // string "mm:ss" or "hh:mm:ss"
        if (is_string($time)) {
            $parts = array_map('intval', explode(':', $time));
            if (count($parts) === 2) {
                return $parts[0] * 60 + $parts[1];
            }
            if (count($parts) === 3) {
                return $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
            }
        }

        return -1;
    }

    private function normalizePath(?string $path): string
    {
        if (!$path) {
            return '';
        }

        $path = Str::of($path)
            ->replaceStart('file://localhost/', '')
            ->replaceStart('file://', '')
            ->trim();

        return urldecode($path);
    }
}
