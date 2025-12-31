<?php

namespace App\Services\Music;

use App\Helpers\SongNameHelper;
use App\Models\Music\Song;
use Illuminate\Support\Str;

class SongFinder
{
    public function findMatch(string $name, string $album, string $artist, float $minScore = 50): array
    {
        $sanitized = [
            'name' => Str::title(SongNameHelper::sanitizeSongNAme($name, 'name_helper_last_fm_to_itunes')) ?? '',
            'album' => Str::title(SongNameHelper::sanitizeSongNAme($album, 'name_helper_last_fm_to_itunes', 'album')) ?? '',
            'artist' => Str::title(SongNameHelper::sanitizeSongNAme($artist, 'name_helper_last_fm_to_itunes', 'artist')) ?? '',
        ];
        $shortName = 'disintegraged';

        // Matching strategies in priority order
        $strategies = [
            ['artistField' => 'artists.name',         'requireAlbum' => true,  'nameShort' => false, 'via' => 'soundex album+artist (+name hint)'],
            ['artistField' => 'songs.album_artist',   'requireAlbum' => true,  'nameShort' => false, 'via' => 'soundex album + album_artist (+name hint)'],
            ['artistField' => 'artists.name',         'requireAlbum' => false, 'nameShort' => false, 'via' => 'soundex artist (+name hint), no album'],
            ['artistField' => 'songs.album_artist',   'requireAlbum' => false, 'nameShort' => false, 'via' => 'soundex album_artist (+name hint), no album'],
            ['artistField' => 'artists.name',         'requireAlbum' => false, 'nameShort' => true, 'via' => 'soundex artist (+name short), no album'],
        ];

        $candidates = collect();
        $via = 'none';

        foreach ($strategies as $strategy) {
            $query = Song::query()
                ->select('songs.*')
                ->leftJoin('albums', 'albums.id', '=', 'songs.album_id')
                ->leftJoin('artists', 'artists.id', '=', 'albums.artist_id')
                ->leftJoin('genres', 'genres.id', '=', 'albums.genre_id')
                ->where('genres.name', '<>', 'Cabaret'); // skip Cabaret

            if ($strategy['requireAlbum'] && $sanitized['album'] !== '') {
                $query->whereRaw('SOUNDEX(albums.name) = SOUNDEX(?)', [$sanitized['album']]);
            }

            if ($sanitized['artist'] !== '') {
                $query->whereRaw('SOUNDEX(' . $strategy['artistField'] . ') = SOUNDEX(?)', [$sanitized['artist']]);
            }

            if ($sanitized['name'] !== '') {

                if ($strategy['nameShort']) {
                    // TO DO
                    $query->whereRaw('SOUNDEX(songs.name) = SOUNDEX(?)', [$shortName]);
                } else {
                    $query->where(function ($q) use ($sanitized) {
                        $q->where('songs.name', 'like', '%' . $sanitized['name'] . '%')
                            ->orWhereRaw('SOUNDEX(songs.name) = SOUNDEX(?)', [$sanitized['name']]);
                    });
                }
            }

            $candidates = $query->with(['album', 'artist'])
                ->orderByRaw('CASE WHEN albums.category_id = 1 THEN 0 ELSE 1 END')
                ->limit(20)->get();

            if ($candidates->isNotEmpty()) {
                $via = $strategy['via'];
                break;
            }
        }

        if ($candidates->isEmpty()) {
            return ['song' => null, 'sanitized' => $sanitized, 'passed' => false, 'nameShort' => false, 'via' => $via, 'score' => 0.0];
        }

        // Weighted similarity scoring
        $weights = ['title' => 0.4, 'artist' => 0.2, 'album' => 0.2];
        $best = null;
        $bestScore = -1;

        foreach ($candidates as $song) {
            $sName = $song->name ?? '';
            $sAlbum = $song->album->name ?? '';
            $sArtist = $song->artist->name ?? ($song->album_artist ?? '');

            $score['name'] = $this->similarityScore($sanitized['name'], $sName) * $weights['title'];
            $score['album'] = $this->similarityScore($sanitized['artist'], $sArtist) * $weights['artist'];
            $score['artist'] = $this->similarityScore($sanitized['album'], $sAlbum) * $weights['album'];

            $score['total'] = $score['name'] + $score['album'] + $score['artist'];

            // Album bonus
            if (($song->album->category_id ?? null) === 1) {
                $score['total'] += 20;
            }

            if ($score['total'] > $bestScore) {
                $bestScore = $score['total'];
                $best = $song;
            }
        }

        if ($bestScore < $minScore) {
            return [
                'song' => null,
                'sanitized' => $sanitized,
                'passed' => false,
                // 'nameShort' => false,
                'via' => $via,
                'status' => 'error',
                'score' => $bestScore,
            ];
        }

        return [
            'song' => $best?->toArray(),
            'sanitized' => $sanitized,
            'passed' => true,
            'via' => $via,
            'status' => 'success',
            'score' => round($bestScore, 2),
        ];
    }

    /**
     * SQL fragment for normalizing a column value in queries.
     * Example: ->orWhereRaw($this->norm('songs.name') . ' LIKE ?', ['%needle%'])
     */
    private function norm(string $column): string
    {
        return "LOWER(TRIM(REGEXP_REPLACE($column, '[^A-Za-z0-9]+', ' ')))";
    }

    /**
     * Normalize a string for comparison against the DB.
     * Removes non-alphanumerics, collapses spaces, lowercases.
     */
    private function prepNeedle(?string $value): string
    {
        if (empty($value)) {
            return '';
        }

        $value = \Illuminate\Support\Str::ascii($value);          // optional: remove accents
        $value = preg_replace('/[^A-Za-z0-9]+/', ' ', $value);    // keep only letters/numbers

        return strtolower(trim($value));
    }

    // put inside SongFinder (public/protected both fine)
    protected function similarityScore(string $a, string $b): float
    {
        $a = strtolower($a);
        $b = strtolower($b);
        $lev = levenshtein($a, $b);

        return max(0, 100 - min($lev, 100)); // 0..100
    }
}
