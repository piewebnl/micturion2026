<?php

namespace App\Console\Commands\ItunesLibrary;

use App\Models\Setting;
use App\Services\Logger\Logger;
use App\Services\Music\SongFinder;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

// php artisan command:ItunesLibraryImportLastFmPlayCounts
class ItunesLibraryImportLastFmPlayCountsCommand extends Command
{
    use QueryCache;

    protected $signature = 'command:ItunesLibraryImportLastFmPlayCounts';

    private string $channel;

    public function handle()
    {
        $this->channel = 'itunes_library_import_last_fm_play_counts';

        Logger::deleteChannel($this->channel);

        Logger::echoChannel($this->channel);

        // Grab a previous date from settings
        $importDate = Setting::getSetting('itunes_library_import_last_fm_play_counts_date');

        /*
        if (! $importDate) {
            Logger::log('info', $this->channel, 'No previous import date found in settings, skipping Last FM PlayCount import.');
            // Logger::echo($this->channel);
            return;
        }
        */

        function lastfmSign(array $params, string $secret): string
        {
            ksort($params);
            $str = '';
            foreach ($params as $k => $v) {
                if ($k === 'format') {
                    continue;
                } // do not include 'format' in signature
                $str .= $k . $v;
            }

            return md5($str . $secret);
        }

        $apiKey = config('lastfm.last_fm_api_key');
        $apiSecret = config('lastfm.last_fm_shared_secret');
        $session = Setting::getSetting('last_fm_session_key');

        $all = [];
        $page = 1;
        $limit = 1000;

        do {
            $params = [
                'method' => 'user.getRecentTracks',
                'user' => 'micturion',
                'from' => \Carbon\Carbon::create(2025, 7, 1, 0, 0, 0, 'UTC')->timestamp,
                'to' => \Carbon\Carbon::create(2025, 7, 31, 23, 59, 59, 'UTC')->timestamp,
                // 'from'    => \Carbon\Carbon::create(2025, 8, 13, 0, 0, 0, 'UTC')->timestamp,
                // 'to'      => \Carbon\Carbon::create(2025, 8, 13, 23, 59, 59, 'UTC')->timestamp,
                'limit' => $limit,
                'page' => $page,
                'api_key' => $apiKey,
                'sk' => $session,
                'format' => 'json',
            ];

            $params['api_sig'] = lastfmSign($params, $apiSecret);

            $res = Http::get('https://ws.audioscrobbler.com/2.0/', $params)->json();
            $tracks = Arr::get($res, 'recenttracks.track', []);
            if (!is_array($tracks)) {
                $tracks = [$tracks];
            }

            $tracks = array_filter($tracks, fn ($t) => isset($t['date']['uts']));
            $all = array_merge($all, $tracks);

            $got = count($tracks);

            $page++;
        } while ($got === $limit);

        // Reverse to get oldest first
        $tracks = array_reverse($all);

        $songs = [];
        foreach ($tracks as $track) {

            $songs[] = [
                'name' => $track['name'] ?? '',
                'artist' => $track['artist']['#text'] ?? '',
                'album' => $track['album']['#text'] ?? '',
                'timestamp' => $track['date']['uts'] ?? null, // timestamp
                'date' => $track['date']['#text'] ?? '',
            ];
        }

        // Lookup persisntent songs in the database
        $songFinder = new SongFinder;
        foreach ($songs as $index => $song) {

            $name = $song['name'] ?? '';
            $album = $song['album'] ?? '';
            $artist = $song['artist'] ?? '';

            $textSearch = $name . ' - ' . $album . ' - ' . $artist;

            $result = $songFinder->findMatch(
                $name,
                $album,
                $artist
            );

            $name = $result['song']['name'] ?? '';
            $persistentId = $result['song']['persistent_id'] ?? '';
            $artist = $result['song']['artist']['name'] ?? '';
            $album = $result['song']['album']['name'] ?? '';
            $score = $result['score'] ?? 0;

            $textFound = $artist . ' - ' . $album . ' - ' . $name . ' - ' . ' PID: $persistentId | Score:  ' . $score['total'];
            $textSanitized = $result['sanitized']['artist'] . ' - ' . $result['sanitized']['album'] . ' - ' . $result['sanitized']['name'];

            if (!$result['passed']) {
                Logger::log(
                    'warning',
                    $this->channel,
                    'Skipping: ' . $textSearch . 'Sanitized: ' . $textSanitized . ', score too low: ' . $score['total'] . ' Details: ' .
                        $score['artist'] . ' ' . $score['album'] . ' ' . $score['name']
                );
            } else {
                if (!isset($result['song']['persistent_id'])) {
                    Logger::log('error', $this->channel, "Error: $index: $textSearch\rSanitized: $textSanitized, no persistent ID found, skipping");
                } else {
                    $playCount = $result['song']['play_count'] ?? 0;
                    Logger::log('info', $this->channel, "Found $textFound , adding play count from $playCount to " . $playCount + 1);
                    // $this->incrementPlayCountByPID($result['song']['persistent_id']);
                }
            }
        }

        // Logger::echo($this->channel);
    }

    private function incrementPlayCountByPID($pid)
    {
        $pid = strtoupper($pid); // persistent IDs are uppercase hex

        $appleScript = <<<EOD
        tell application "Music"
            activate
            set t to first track of library playlist 1 whose persistent ID is "$pid"
            set oldCount to played count of t
            set played count of t to (oldCount + 1)
        end tell
    EOD;

        shell_exec('osascript -e ' . escapeshellarg($appleScript));
    }
}
