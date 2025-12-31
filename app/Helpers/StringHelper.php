<?php

namespace App\Helpers;

// General string helper class
class StringHelper
{
    public static function findInString(string $haystack, array $needle, int $offset = 0): bool
    {

        if (!is_array($needle)) {
            $needle = [$needle];
        }

        foreach ($needle as $searchstring) {
            $position = stripos($haystack, $searchstring, $offset);

            if ($position !== false) {
                return true;
            }
        }

        return false;
    }

    public static function similarName($searchName, $foundName)
    {
        similar_text(strtolower($searchName), strtolower($foundName), $perc);

        return $perc;
    }
}
