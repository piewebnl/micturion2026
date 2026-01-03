<?php

namespace App\Services\Discogs\Matchers;

use App\Models\Discogs\DiscogsRelease;
use App\Models\Discogs\DiscogsReleaseCustomId;
use App\Models\DiscogsApi\DiscogsApiCollectionRelease;
use App\Models\Music\Album;
use App\Services\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Http\JsonResponse;

class DiscogsCollectionMatcher
{
    private JsonResponse $response;

    private $collectionRelease;

    private $customReleaseIds;

    private Command $command;

    private string $channel = 'discogs_collection_matcher';

    public function __construct(?Command $command = null)
    {
        $this->command = $command;
        $this->customReleaseIds = DiscogsReleaseCustomId::all();
        $this->collectionRelease = new DiscogsApiCollectionRelease;
    }

    public function match(DiscogsRelease $release): ?array
    {
        $release['format'] = $release->format ?? null;

        // Any custom ids?
        $custom = $this->customReleaseIds->firstWhere('release_id', $release['release_id']);

        if ($custom) {
            $album = Album::with('artist')->where('persistent_id', $custom['persistent_album_id'])->first();

            if (!$album) {
                Logger::log(
                    'error',
                    $this->channel,
                    'Custom ID used, but album NOT found for release ID ' . $release['id'] .
                        ' with ' . $custom['persistent_album_id'],
                    [],
                    $this->command
                );

                return null;
            }

            Logger::log(
                'notice',
                $this->channel,
                'Custom ID used for: ' . $album->artist->name . ' - ' . $album->name . ' [' . $release['release_id'] . ']',
                [],
                $this->command
            );
            $release['album_id'] = $album->id;
            $release['status'] = 'custom';
            $release['score'] = 100;

            return $release->toArray();
        }

        $match = $this->findMatch($release);
        if ($match) {
            Logger::log(
                'info',
                $this->channel,
                'Discogs Release matched: ' . $match['album']->artist->name . ' - ' . $match['album']->name . ' [' . $release['release_id'] . ']',
                [],
                $this->command
            );
            $release['album_id'] = $match['album']->id;
            $release['status'] = 'matched';
            $release['score'] = max(0, round($match['score']) - 1);
        }

        return $release->toArray();
    }

    public function findMatch($release): ?array
    {

        $results = Album::query()
            ->join('artists', 'albums.artist_id', '=', 'artists.id')
            ->whereRaw('SOUNDEX(albums.name) = SOUNDEX(?)', [$release->title])
            ->whereRaw('SOUNDEX(artists.name) = SOUNDEX(?)', [$release->artist])
            ->select('albums.*', 'artists.name as artist_name')
            ->get();

        if ($results->isEmpty()) {
            return null;
        }

        $scored = $results->map(function ($album) use ($release) {
            $titleScore = $this->similarityScore($release->title, $album->name);
            $artistScore = $this->similarityScore($release->artist, $album->artist->name);

            return [
                'album' => $album,
                'score' => ($titleScore * 0.5) + ($artistScore * 0.5), // Weighted: prioritize title more
            ];
        });

        return $scored->sortByDesc('score')->first();
    }

    public function similarityScore(string $a, string $b): int
    {
        $score = levenshtein(strtolower($a), strtolower($b));

        return max(0, 100 - min($score, 100));
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }

    public function storeMatches(array $processedReleases): int
    {
        return $this->collectionRelease->storeAllFromDiscogsReleaseMatcher($processedReleases);
    }

    public function handleSkipped(): void
    {
        $skipped = DiscogsReleaseCustomId::where('release_id', 'skipped')->get();

        foreach ($skipped as $skip) {
            $album = Album::where('persistent_id', $skip['persistent_album_id'])->first();

            if (!$album) {
                continue;
            }
            $discogsRelease = DiscogsRelease::where('album_id', $album->id)->first();

            if ($discogsRelease) {
                $discogsRelease->release_id = 0;
                $discogsRelease->score = 0;
                $discogsRelease->save();
                Logger::log(
                    'warning',
                    $this->channel,
                    'Skip Discogs Release: ' . $discogsRelease->artist . ' - ' . $discogsRelease->title,
                    [],
                    $this->command
                );
            }
        }
    }
}
