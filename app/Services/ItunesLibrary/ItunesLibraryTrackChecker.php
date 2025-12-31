<?php

namespace App\Services\ItunesLibrary;

// Check if some field info is correct (naming/formatting)
class ItunesLibraryTrackChecker
{
    private $track;

    public function __construct($track)
    {
        $this->track = $track;
    }

    // Check if some field info is correct (naming/formatting)
    public function checkFields()
    {
        $itunesLibraryNameChecker = new ItunesLibraryNameChecker;

        // Check Album sort name
        if (!$itunesLibraryNameChecker->checkSortAlbumName($this->track)) {
            $resource = $itunesLibraryNameChecker->getResource();
            $this->track['text'] = $resource['text'];
            $this->track['status'] = 'warning';
        }

        return $this->track;
    }

    public function getTrack()
    {
        return $this->track;
    }
}
