<?php

namespace App\Services\Discogs\Matchers;

use App\Models\Discogs\DiscogsRelease;
use App\Models\Discogs\DiscogsReleaseCustomId;
use App\Models\DiscogsApi\DiscogsApiCollectionRelease;
use App\Models\Music\Album;
use App\Traits\Logger\Logger;
use Illuminate\Http\JsonResponse;

class DiscogsCollectionMatcher
{
    private JsonResponse $response;

    private $collectionReleases;

    private $collectionRelease;

    private $customReleaseIds;

    public function __construct()
    {
        $this->customReleaseIds = DiscogsReleaseCustomId::all();
        $this->collectionRelease = new DiscogsApiCollectionRelease;
    }

    public function match(): void
    {
        $processedReleases = collect();

        $this->collectionReleases = DiscogsRelease::all();

        foreach ($this->collectionReleases as $release) {

            $match = $this->findMatch($release);

            $release['format'] = $release->format ?? null;

            if ($match) {
                $release['album_id'] = $match['album']->id;
                $release['score'] = max(0, round($match['score']) - 1);
            }

            $custom = $this->customReleaseIds->firstWhere('release_id', $release['id']);
            if ($custom) {
                $album = Album::where('persistent_id', $custom['persistent_album_id'])->first();

                if (!$album) {
                    Logger::log(
                        'error',
                        'discogs_collection_importer',
                        'Custom ID used, but album NOT found for release ID ' . $release['id'] .
                            ' with ' . $custom['persistent_album_id']
                    );

                    continue; // Skip this release
                }

                $release['album_id'] = $album->id;
                $release['score'] = 100;
            }

            $processedReleases->push($release);
        }

        $this->collectionRelease->storeAllFromDiscogsReleaseMatcher($processedReleases->toArray());

        //$this->response = response()->success('Discogs collection imported', $this->collectionReleases);
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
}
