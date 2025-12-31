<?php

namespace App\Services\ItunesLibrary;

use App\Models\Music\Song;

class ItunesLibraryNameChecker
{
    private $configValues;

    private $resource;

    public function __construct()
    {
        $this->configValues = config('ituneslibrary.itunes_name_checker');
    }

    // Check if some field info is correct (naming/formatting)
    public function checkSortAlbumName($track)
    {

        $pattern = '/([0-9]{4}([A-z])?\s-\s)([0-9]{4}([A-z])?\s-\s)?./m';
        if (!preg_match($pattern, $track['sort_album'], $matches)) {
            $this->resource['text'] = 'Faulty sort album format 9999(a) - 9999(a) - {Name}: ' . $track['sort_album'];

            return false;
        }

        return true;
    }

    public function checkTitleCase(Song $song, string $type)
    {

        $this->resource = null;

        $name = $song->name;
        if ($type == 'artist') {

            $name = $song->artist->name;
        }

        if ($type == 'album') {
            $name = $song->album->name;
        }

        if ($type == 'song') {
            $name = $song->name;
        }

        $this->checkName($name, $song, $type);

        return true;
    }

    private function checkName($name, Song $song, $type)
    {
        $pattern = '/(([A-Z\p{Lu}0-9])([a-z0-9\p{L}\/,.\'-_\!\?]+))\s|([A-Z\p{Lu}0-9\&\s])/mu';

        $words = explode(' ', $name);
        if (!in_array($name, $this->configValues[$type])) {
            foreach ($words as $word) {
                if (strlen($word) > 1 and !in_array($word, $this->configValues['exception_words'])) {
                    if (!preg_match($pattern, $word, $matches)) {
                        $this->resource['text'] = 'Wrong case in field ' . $type . ' in the word: "' . $word . '" in: ' . $song->track_number . '. ' . $song->artist->name . ' - ' . $song->album->name . ' - ' . $song->name;

                        return false;
                    }
                }
            }
        }
    }

    // Check (Live) (Alternative) etc...
    public function checkExtraInfo(Song $song)
    {
        $lower = strtolower($song->name);

        foreach ($this->configValues['extra_info'] as $word) {
            $wordLowerCase = strtolower($word);

            if (str_contains($lower, $wordLowerCase)) {
                $grab = substr($song->name, stripos($lower, $wordLowerCase), strlen($word));
                if ($grab != $word) {
                    $this->resource['text'] = 'Wrong info found in song name: ' . $grab . ' (must be ' . $word . ')  in: ' . $song->track_number . '. ' . $song->artist->name . ' - ' . $song->album->name . ' - ' . $song->name;

                    return false;
                }
            }
        }

        return true;
    }

    public function getResource(): ?array
    {
        return $this->resource;
    }
}
