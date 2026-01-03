<?php

namespace App\Services\ItunesLibrary;

use App\Models\Music\Album;
use App\Models\Music\Artist;
use App\Models\Music\SkippedSong;
use App\Models\Music\Song;
use App\Services\Logger\Logger;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ItunesLibraryChecker
{
    private $resource;

    private $itunesLibraryNameChecker;

    private $song;

    private $audioExtensions = ['mp3', 'flac', 'm4a', 'aac', 'wav', 'ogg', 'alac', 'aiff'];

    // X Check Names
    // X Check overal disk numbers
    // X Check tracklisting in order
    // X Amount of tracks null (nog in DB!)
    //   Check Mix goed is (alles trak no.1)
    // X Check only one song one artist -> Mix!
    //   Check of er een folder.jpg is
    //   Check Empty folder
    //   Scan files match amount
    //   BSides and Misc... must be (Bootleg)
    //   BSides and Misc... year = empty
    //   Check owned -> Apple lossless?
    //   Check if not on MAcBook local drive

    public function __construct()
    {
        $this->itunesLibraryNameChecker = new ItunesLibraryNameChecker;

        $this->song = new Song;
    }

    public function checkSongs()
    {
        $filterValues = [];

        $songs = $this->song->getSongsWithoutSpotifyTrack($filterValues);

        $this->checkTrackNumbers($songs);

        // Check all kinds of stuff
        foreach ($songs as $song) {

            $this->checkDiscSet($song);

            $namesToCheck = ['artist', 'album', 'name'];

            foreach ($namesToCheck as $name) {
                $this->itunesLibraryNameChecker->checkTitleCase($song, $name);
                $this->resource = $this->itunesLibraryNameChecker->getResource();
                if ($this->resource) {
                    Logger::log('warning', 'itunes_library_checker', $this->resource['text']);
                }
            }
            if (!$this->itunesLibraryNameChecker->checkExtraInfo($song)) {
                $this->resource = $this->itunesLibraryNameChecker->getResource();
                Logger::log('warning', 'itunes_library_checker', $this->resource['text']);
            }
        }
        unset($songs);
    }

    public function checkFolderImages()
    {

        $basePath = config('music.music_path');

        $audioExtensions = $this->audioExtensions;

        $albums = Album::with('artist')->get();

        foreach ($albums as $album) {
            $albumPath = $basePath . $album->location;
            $folderImage = rtrim($albumPath, '/\\') . DIRECTORY_SEPARATOR . 'folder.jpg';

            if (!File::isDirectory($albumPath)) {
                continue;
            }

            $files = File::files($albumPath);

            $hasAudio = collect($files)->contains(function ($file) use ($audioExtensions) {
                return in_array(strtolower($file->getExtension()), $audioExtensions);
            });

            if ($hasAudio && !file_exists($folderImage)) {
                $message = 'folder.jpg not found in: ' . $album->location;
                $this->resource['text'] = $message;
                Logger::log('warning', 'itunes_library_checker', $message);
            }
        }
    }

    public function checkForMix()
    {
        $artists = Artist::has('albums', '=', 1)
            ->has('songs', '=', 1)
            ->get();

        foreach ($artists as $artist) {
            $this->resource['text'] = 'Should be on Mix compilation: ' . $artist->name;
            if ($this->resource) {
                Logger::log('warning', 'itunes_library_checker', $this->resource['text']);
            }
        }
    }

    public function checkDiscSet(Song $song)
    {

        if (!$song->disc_number) {
            $this->resource['text'] = 'Disc number not set: ' . $song->disc_number . '  ' . $song->track_number . '. ' . $song->artist->name . ' - ' . $song->album->name . ' - ' . $song->name;
            if ($this->resource) {
                Logger::log('warning', 'itunes_library_checker', $this->resource['text']);
            }
        }

        if (!$song->disc_count) {
            $this->resource['text'] = 'Disc count not set: ' . $song->disc_number . '  ' . $song->track_number . '. ' . $song->artist->name . ' - ' . $song->album->name . ' - ' . $song->name;
            if ($this->resource) {
                Logger::log('warning', 'itunes_library_checker', $this->resource['text']);
            }
        }
    }

    public function checkTrackNumbers($songs)
    {
        $trackNumber = 1;
        $discNumber = 1;
        $oldSortAlbum = '';

        foreach ($songs as $key => $song) {

            // echo $song->name . "\r\n";
            // echo $song->category_id . "\r\n";
            // echo $song->album_sort_name . ' ' . $oldSortAlbum . "\r\n";

            if ($song->album_sort_name != $oldSortAlbum) {
                $trackNumber = 1;
                $discNumber = 1;
            }

            if ($song->category_id != 7) {

                if ($song->disc_number != $discNumber) {
                    $discNumber++;
                    $trackNumber = 1;
                }

                // echo $song->disc_number . ' ' . $discNumber . "\r\n";
                // echo $song->track_number . ' ' . $trackNumber . "\r\n";;

                if ($song->track_number != $trackNumber) {
                    echo 'Incorrect track number: ' . $song->track_number . '. vs ' . $trackNumber . ' ' . $song->artist->name . ' - ' . $song->album->name . ' - ' . $song->name . "\r\n";
                    $this->resource['text'] = 'Incorrect track number: ' . $song->disc_number . '  ' . $song->track_number . '. ' . $song->artist->name . ' ' . $song->album->name . ' ' . $song->name;
                    Logger::log('warning', 'itunes_library_checker', $this->resource['text']);
                }

                if ($song->track_count) {
                    $this->resource['text'] = 'Track count IS set (keep empty): ' . $song->disc_number . '  ' . $song->track_number . '. ' . $song->artist->name . ' - ' . $song->album->name . ' - ' . $song->name;
                    if ($this->resource) {
                        Logger::log('warning', 'itunes_library_checker', $this->resource['text']);
                    }
                }

                $trackNumber++;
            }

            $oldSortAlbum = $song->album_sort_name;
        }
    }

    public function checkEmptyFolders()
    {
        $rootFolder = config('music.music_path');
        $audioExtensions = $this->audioExtensions;

        if (!File::isDirectory($rootFolder)) {
            return;
        }

        // Recursively get all directories
        $directories = collect($this->getAllSubdirectories($rootFolder));

        $directories->each(function ($dir) use ($audioExtensions) {
            // Skip if this folder has subdirectories — it's not a "leaf"
            if (File::directories($dir)) {
                return;
            }

            // Check for audio files
            $files = File::files($dir);
            $hasAudio = collect($files)->contains(function ($file) use ($audioExtensions) {
                return in_array(strtolower($file->getExtension()), $audioExtensions);
            });

            if (!$hasAudio) {
                $message = "Empty folder (no audio files here): $dir";
                $this->resource['text'] = $message;
                Logger::log('warning', 'itunes_library_checker', $message);
            }
        });
    }

    public function checkForLostFiles()
    {
        $audioExtensions = $this->audioExtensions;
        $musicPath = config('music.music_path');
        $musicLostFilesPath = config('music.music_lost_files_path');

        if (!File::isDirectory($musicPath)) {
            return;
        }

        $songPaths = Song::with('album')
            ->get()
            ->pluck('location');

        $skippedPaths = SkippedSong::pluck('location');

        $dbPaths = $songPaths
            ->merge($skippedPaths)
            ->map(fn($path) => strtolower($path))
            ->filter()
            ->sort()
            ->toArray();

        file_put_contents('/Users/micturion/Desktop/db_paths.txt', implode("\r\n", $dbPaths));

        $diskFiles = collect(File::allFiles($musicPath))
            ->filter(function ($file) use ($audioExtensions) {
                return in_array(strtolower($file->getExtension()), $audioExtensions);
            })
            ->map(function ($file) {
                $path = strtolower(str_replace(config('music.music_path'), '', $file));

                return $path;
            })
            ->filter()
            ->values()
            ->sort()
            ->toArray();

        file_put_contents('/Users/micturion/Desktop/diskFiles.txt', implode("\r\n", $diskFiles));

        $notPreset = [];

        foreach ($diskFiles as $diskFile) {
            if (!in_array($diskFile, $dbPaths)) {
                $notPreset[] = $diskFile;

                $destinationPath = $musicLostFilesPath . '/' . $diskFile;
                $destinationDir = dirname($destinationPath);

                // Maak doelmap aan
                if (!is_dir($destinationDir)) {
                    mkdir($destinationDir, 0777, true);
                }

                // Verplaats bestand
                if (!file_exists($destinationPath)) {
                    Logger::log('error', 'itunes_library_checker', 'Audio file NOT present in DB - File should be moved: ' . $diskFile);
                    /*
                    if (!rename($musicPath . '/' . $diskFile, $destinationPath)) {
                        Logger::log('error', 'itunes_library_checker', 'Audio file NOT present in DB - Failed to move file: ' . $diskFile);
                    } else {
                        Logger::log('info', 'itunes_library_checker', 'Audio file NOT present in DB - Moved file to: ' . $destinationPath);
                    }
                    */
                } else {
                    Logger::log('error', 'itunes_library_checker', 'Audio file NOT present in DB - File already exists at destination: ' . $destinationPath);
                }
            } else {
                // Logger::log('info', 'itunes_library_checker', 'Audio file present DB: ' . $diskFile);
            }
        }

        sort($notPreset, SORT_NATURAL | SORT_FLAG_CASE);
        file_put_contents('/Users/micturion/Desktop/not_found.txt', implode("\r\n", $notPreset));
    }

    protected function getAllSubdirectories($path)
    {
        $all = [];

        foreach (File::directories($path) as $dir) {
            $all[] = $dir;
            $all = array_merge($all, $this->getAllSubdirectories($dir));
        }

        return $all;
    }

    protected function getAllFiles($path, array $extensions)
    {
        return collect(File::allFiles($path))
            ->filter(function ($file) use ($extensions) {
                return in_array(strtolower($file->getExtension()), $extensions);
            })
            ->map(function ($file) {
                return $file->getRealPath();
            })
            ->all();
    }

    public function getResource(): array
    {
        return $this->resource;
    }
}
