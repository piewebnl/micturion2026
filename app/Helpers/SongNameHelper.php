<?php

namespace App\Helpers;

// Do some string mutations on track names
class SongNameHelper
{
    public static function sanitizeSongName($name, $config, $type = 'song')
    {
        // 1) Remove bracketed qualifiers → single space to avoid gluing words
        // $name = preg_replace('/\([^)]*\)/u', ' ', $name);      // (…)
        // $name = preg_replace('/\[[^\]]*\]/u', ' ', $name);     // […]

        // 2) Remove postfix after a separator " - " (but keep intra-word hyphens)
        // e.g., "Song Title - 2011 Remaster" → "Song Title"
        $name = preg_replace('/\s-\s.*$/u', ' ', $name);

        // 3) Remove standalone 4-digit years (optional – keep if you want)
        $name = preg_replace('/\b(19|20)\d{2}\b/u', ' ', $name);

        // 4) Known junk tokens (case-insensitive)
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
        foreach ($unwanted as $token) {
            $name = preg_replace('/\b' . preg_quote($token, '/') . '\b/i', ' ', $name);
        }

        // 5) Your custom mapping
        $name = self::replaceSong($name, $config, $type);

        // 6) FINAL normalize: collapse ALL whitespace and trim both ends
        $name = trim(preg_replace('/\s+/u', ' ', $name));

        return $name;
    }

    public static function isRemaster($name)
    {
        if (StringHelper::findInString($name, ['Remaster', 'Remastered', '(Deluxe', ' version']) !== false) {
            return true;
        }

        return false;
    }

    public static function replaceSong(string $name, string $config, $type = 'song')
    {
        $replacements = config('music.' . $config, []);

        if (is_array($replacements[$type]) && count($replacements[$type]) > 0) {
            foreach ($replacements[$type] as $search => $replace) {
                $name = str_replace($search, $replace, $name);
            }
        }

        return $name;
    }
}
