<?php

namespace App\Traits\Messages;

trait ItunesLibraryTrackMessage
{
    // Rename a spotify search album result to itunes album (for better match result)
    public static function setMessage(string $message, array $track): string
    {
        $fields = [];
        if (isset($track['artist'])) {
            $fields[] = $track['artist'];
        }
        if (isset($track['Artist'])) {
            $fields[] = $track['Artist'];
        }
        if (isset($track['album'])) {
            $fields[] = $track['album'];
        }
        if (isset($track['Album'])) {
            $fields[] = $track['Album'];
        }
        if (isset($track['name'])) {
            $fields[] = $track['name'];
        }
        if (isset($track['Name'])) {
            $fields[] = $track['Name'];
        }
        $message .= implode(' - ', $fields);

        return $message;
    }
}
