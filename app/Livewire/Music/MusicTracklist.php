<?php

namespace App\Livewire\Music;

use App\Models\Music\Song;
use App\Traits\QueryCache\QueryCache;
use Livewire\Component;

class MusicTracklist extends Component
{
    use QueryCache;

    public $albumId;

    public $showLastFmScrobble = false;

    public function placeholder()
    {
        return <<<'HTML'
        <div>
           <x-load-more.spinner />
        </div>
        HTML;
    }

    public function render()
    {

        // $songs = $this->getCache('tracklist_' . $this->albumId, []);
        // if (!$songs) {
        $songs = Song::where('album_id', $this->albumId)
            ->orderBy('disc_number')
            ->orderBy('track_number')->get(['id', 'name', 'album_artist', 'track_number', 'disc_number', 'grouping', 'time', 'rating']);

        //  $this->setCache('tracklist_' . $this->albumId, [], $songs);
        // }

        return view('livewire.music.music-tracklist', compact('songs'));
    }
}
