<?php

namespace App\Services\ItunesLibrary;

use App\Models\Music\Category;
use App\Models\Music\Format;
use App\Traits\Logger\Logger;
use App\Traits\Messages\ItunesLibraryTrackMessage;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

// Do some mutation on library track
class ItunesLibraryTrackMutator
{
    use ItunesLibraryTrackMessage;

    private $track;

    private $format;

    private $category;

    private $musicPath;

    public function __construct()
    {

        $this->format = new Format;
        $this->category = new Category;
        $this->musicPath = config('music.music_path');
    }

    // Set the track, do mutations and determine if valid for import
    public function doMutate(array $track)
    {

        $this->setTrack($track);
        $this->track['status'] = 'success';

        // Manipulate some stuff
        try {
            $this->convertKeys();
            $this->setDefaults([
                'grouping',
                'compilation',
                'disc_count',
                'track_count',
                'favourite',
                'track_id',
                'play_count',
                'album_artist',
                'sort_album_artist',
                'sort_artist',
                'location',
                'is_compilation',
                'total_time',
                'sort_album',
                'rating',
            ]);
            $this->track['sort_artist'] = $this->setSortArtist($this->track['artist'], $this->track['sort_artist']);
            $this->handleVariousArtist();
            $this->setAlbumArtworkSlug();
            $this->setAlbumPersistentId();
            $this->setTime();
            $this->setSongLocation();
            $this->setAlbumLocation();
            $this->setCategory();
            $this->setHasChanged();
            $this->setFormat();
            $this->setTimeMs();
            $this->setNotes();
            $this->setDates();
        } catch (Exception $e) {
            dd($e);
        }

        // Import at all?
        $itunesLibraryTrackValidator = new ItunesLibraryTrackValidator($this->track);
        $this->track = $itunesLibraryTrackValidator->validForImport();

        if ($this->track['status'] == 'warning') {
            return;
        }

        $this->track = $itunesLibraryTrackValidator->checkTrack();
        if ($this->track['status'] == 'error') {
            return;
        }

        $itunesLibraryTrackChecker = new ItunesLibraryTrackChecker($this->track);
        $this->track = $itunesLibraryTrackChecker->checkFields();

        if ($this->track['status'] == 'error') {
            return;
        }

        $this->track['text'] = 'Imported';
    }

    public function getTrack()
    {
        return $this->track;
    }

    public function setTrack(array $track)
    {
        $this->track = $track;

        return $this;
    }

    // Give a null value, if not set or empty value
    private function setDefaults(array $fieldNames)
    {

        foreach ($fieldNames as $fieldName) {
            if (isset($this->track[$fieldName]) && $fieldName == 'rating') {
                if ($this->track[$fieldName] == 1) {
                    $this->track[$fieldName] = null;
                }
                if (isset($this->track['rating_computed'])) {
                    $this->track[$fieldName] = null;
                }
            }
            if (!isset($this->track[$fieldName]) || $this->track[$fieldName] == '') {
                if ($fieldName == 'play_count') {
                    $this->track[$fieldName] = 0;
                } else {
                    $this->track[$fieldName] = null;
                }
            }
        }
    }

    // Convert iTunes keys to mySql snake_case
    private function convertKeys()
    {

        foreach ($this->track as $key => $value) {
            $newKey = str_replace(' ', '_', strtolower($key));
            $newTrack[$newKey] = $value;
        }
        $this->track = $newTrack;
    }

    private function setDates()
    {
        if (!isset($this->track['date_added'])) {

            $this->track['status'] = 'error';
            $this->track['text'] = ItunesLibraryTrackMessage::setMessage('no date added: ', $this->track);
            Logger::log('error', 'iTunes library import tracks', $this->track['text']);

            return false;
        }

        $this->track['date_added'] = Carbon::parse($this->track['date_added'])->format('Y-m-d H:i:s');

        if (!isset($this->track['date_modified'])) {

            $this->track['status'] = 'error';
            $this->track['text'] = ItunesLibraryTrackMessage::setMessage('no date modified: ', $this->track);
            Logger::log('error', 'iTunes library import tracks', $this->track['text']);

            return false;
        }

        $this->track['date_modified'] = Carbon::parse($this->track['date_modified'])->format('Y-m-d H:i:s');
        // dd($this->track['date_modified']);
    }

    // Get the folders only like: /{artist}/{album}/
    private function setAlbumLocation()
    {

        $parts = explode('/', $this->track['location']);
        $folders = array_slice($parts, -3, 2);

        if (count($folders) == 2) {
            $this->track['album_location'] = '/' . $folders[0] . '/' . $folders[1] . '/';
        }
    }

    // Get the folders only like: /{artist}/{album}/{filename}
    private function setSongLocation()
    {

        if ($this->track['location'] != '') {
            $parts = preg_split('~/+~u', trim($this->track['location'], '/'));

            $artist = $parts[count($parts) - 3] ?? '';
            $album = $parts[count($parts) - 2] ?? '';
            $file = $parts[count($parts) - 1] ?? '';
            $this->track['location'] = '/' . implode('/', [$artist, $album, $file]);
        }
    }

    private function setSortArtist(string $artist, ?string $sortArtist)
    {
        if ($sortArtist == null or $sortArtist == '') {
            $sortArtist = $artist;
        }

        return $sortArtist;
    }

    private function setAlbumArtworkSlug()
    {

        $this->track['artwork_slug'] = str::slug($this->track['artist'] . '-' . $this->track['sort_album']);
    }

    private function setAlbumPersistentId()
    {
        $this->track['persistent_album_id'] = strtoupper(md5(strtoupper($this->track['artist']) . strtoupper($this->track['sort_album'])));
    }

    // When track is part of compilation, alter some fields
    private function handleVariousArtist()
    {
        if ($this->track['compilation'] == true) {
            $this->track['album_artist'] = $this->track['artist'];
            $this->track['artist'] = 'Various Artists';
            $this->track['sort_artist'] = 'Various Artists';
        }

        return $this->track;
    }

    // Set total track time to readable format
    private function setTime()
    {

        if (!isset($this->track['total_time']) or $this->track['total_time'] == null) {
            return 'no time';
        }

        if ($this->track['total_time'] >= 3600000) {
            return date('H:i:s', round($this->track['total_time'] / 1000) - (60 * 60));
        }

        $this->track['time'] = date('i:s', round($this->track['total_time'] / 1000));
    }

    private function setCategory()
    {

        $category = $this->category->getCategoryByGrouping($this->track['grouping']);

        if (empty($category)) {
            $this->track['status'] = 'error';
            $this->track['text'] = ItunesLibraryTrackMessage::setMessage('No grouping found: ', $this->track);
            Logger::log('error', 'iTunes library import tracks', $this->track['text']);

            return false;
        }

        $this->track['category_id'] = $category['id'];
        $this->track['category'] = $category['name'];
    }

    private function setFormat()
    {
        $this->track['formats'] = $this->format->getFormatsByGrouping($this->track['grouping']);
    }

    private function setTimeMs()
    {
        if ($this->track['total_time'] > 0) {
            $this->track['time_ms'] = floor($this->track['total_time'] / 1000) * 1000;
        }
    }

    private function setNotes()
    {
        $this->track['notes'] = '';
        if (!empty($this->track['grouping']) && is_string($this->track['grouping'])) {
            if (strpos($this->track['grouping'], '(with Artwork)') !== false) {
                $this->track['notes'] = 'CDR with original artwork';
            }
        }
    }

    private function setHasChanged()
    {
        $this->track['has_changed'] = true;
    }
}
