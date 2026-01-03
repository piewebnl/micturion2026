<?php

namespace App\Services\ItunesLibrary;

use App\Helpers\StringHelper;
use App\Services\Logger\Logger;
use App\Traits\Messages\ItunesLibraryTrackMessage;

// Do some checks on library track and determine if needs to be imported
class ItunesLibraryTrackValidator
{
    use ItunesLibraryTrackMessage;

    private $track;

    protected $ignorePath = [
        '/TV Shows',
        '/Movies',
        '/Podcasts',
        '/Voice Memos',
        '/Tones',
        '/Audiobooks',
    ];

    public function __construct($track)
    {
        $this->track = $track;
    }

    // Check if track stuff is set
    public function checkTrack()
    {

        if (!isset($this->track['artist'])) {
            $this->track['status'] = 'error';
            $this->track['text'] = ItunesLibraryTrackMessage::setMessage('Artist not set: ', $this->track);
            Logger::log('error', 'itunes_library_importer', $this->track['text']);
        }

        if (!isset($this->track['sort_album'])) {
            $this->track['status'] = 'error';
            $this->track['text'] = ItunesLibraryTrackMessage::setMessage('Album sort name not set: ', $this->track);
            Logger::log('error', 'itunes_library_importer', $this->track['text']);
        }

        if (isset($this->track['track_number']) and !$this->track['track_number'] > 0) {
            $this->track['text'] = 'Track number not set: ';
            $this->track['status'] = 'error';
            Logger::log('error', 'itunes_library_importer', $this->track['text']);
        }

        if (isset($this->track['disc_number']) and $this->track['disc_number'] == '') {
            $this->track['text'] = $this->track['text'] = ItunesLibraryTrackMessage::setMessage('Disc number not set: ', $this->track);
            $this->track['status'] = 'error';
            Logger::log('error', 'itunes_library_importer', $this->track['text']);
        }

        return $this->track;
    }

    // Check wether song sould be imported or not (throws warning)
    public function validForImport()
    {
        if (isset($this->track['is_extra']) and $this->track['is_extra']) {
            $this->track['status'] = 'success';

            return $this->track;
        }

        if (isset($this->track['composer']) and $this->track['composer'] == 'xxx - skip') {
            $this->track['text'] = ItunesLibraryTrackMessage::setMessage('Marked as xxx - skip: ', $this->track);
            $this->track['status'] = 'warning';
        }

        if (isset($this->track['genre']) and $this->track['genre'] == 'Cabaret') {
            $this->track['text'] = ItunesLibraryTrackMessage::setMessage('Genre = Caberet: ', $this->track);
            $this->track['status'] = 'warning';
        }

        if (isset($this->track['genre']) and $this->track['genre'] == 'Games') {
            $this->track['text'] = ItunesLibraryTrackMessage::setMessage('Genre = Games', $this->track);
            $this->track['status'] = 'warning';
        }

        if (
            isset($this->track['location']) and
            StringHelper::findInString($this->track['location'], $this->ignorePath) > 0
        ) {
            $this->track['text'] = ItunesLibraryTrackMessage::setMessage('Ingored folder', $this->track);
            $this->track['status'] = 'warning';
        }

        return $this->track;
    }

    public function getTrack()
    {
        return $this->track;
    }
}
