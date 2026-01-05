<?php

namespace App\Services\Spotify\Helpers;

use App\Helpers\StringHelper;

// Do some string mutations on Spotify track names
class SpotifyNameHelper
{
    public function santizeSpotifyName($name)
    {

        // Remove all between ( ) and [ ]
        $name = preg_replace("/\([^)]+\)/", '   ', $name);
        $name = preg_replace("/\[[^)]+\]/", '', $name);

        // Remove all behind -
        $split = explode('-', $name);
        if (isset($split[0])) {
            $name = $split[0];
        }

        $name = preg_replace("/\d{4}/", '', $name);
        $name = str_replace('  ', ' ', $name);
        $name = rtrim($name, ' ');

        $unwanted = [
            'Remastered Version',
            'Remastered',
            'Remaster',
            'Edit',
            'Deluxe',
            'Deluxe Version',
            'Deluxe Edition',
            'Special Edition',
            'Extended',
            'Legacy',
            'Expanded',
        ];

        foreach ($unwanted as $string) {
            $name = str_ireplace($string, '', $name);
        }

        return $name;
    }

    public function sanitzeSpotifyArtist($name)
    {

        $sanitizedArtistNames = config('spotify.sanitize_artist_names', []);
        if ($sanitizedArtistNames) {
            $name = str_replace(array_keys($sanitizedArtistNames), array_values($sanitizedArtistNames), $name);
        }

        return $name;
    }

    public function isRemaster($name)
    {
        if (StringHelper::findInString($name, ['Remaster', 'Remastered', '(Deluxe', ' version']) !== false) {
            return true;
        }

        return false;
    }

    public function similarityScore($searchTrackName, $foundName)
    {
        // How many replacemants does it take? 0+ charaters
        $dif = levenshtein(strtolower($searchTrackName), strtolower($foundName));
        if ($dif > 100) {
            $dif = 100;
        }

        return round(100 - $dif);
    }

    public function areNamesSimilar($searchTrackName, $foundName)
    {

        similar_text(strtolower($searchTrackName), strtolower($foundName), $perc);

        return $perc;
    }
}
