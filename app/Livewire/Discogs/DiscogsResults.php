<?php

namespace App\Livewire\Discogs;

use App\Livewire\Forms\Discogs\DiscogsSearchFormInit;
use App\Models\Discogs\DiscogsRelease;
use App\Models\Discogs\DiscogsReleaseCustomId;
use App\Models\Music\Album;
use App\Traits\Forms\SearchForm;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class DiscogsResults extends Component
{
    use SearchForm;

    private DiscogsSearchFormInit $searchFormInit;

    #[Url(as: 's', keep: false, history: true)]
    public $filterValues = [];

    public $searchFormData;

    private $perPage = 30;

    public function boot()
    {
        $this->searchFormInit = new DiscogsSearchFormInit($this->searchFormData);
        $this->filterValues = $this->searchFormInit->init($this->filterValues);
    }

    public function loadMore()
    {
        $this->filterValues['per_page'] += $this->perPage;
    }

    #[On('discogs-results-save-discogs-release')]
    public function saveDiscogsRelease(string $discogsReleaseId, string $albumId)
    {

        // get the album
        $album = Album::where('id', $albumId)->with('artist', 'formats')->first();

        // get the release
        $discogsRelease = DiscogsRelease::where('id', $discogsReleaseId)->first();

        if ($album && $discogsRelease) {

            $discogsRelease->fill([
                'id' => $discogsReleaseId,
                'album_id' => $album['id'],
                // 'release_id' => $discogsReleaseId,
                // 'artist' => $album['artist']['name'],
                // 'title' => $album['name'],
                'score' => 100,
                // 'format' => $format ?? "",
            ]);
            $discogsRelease->save();

            // Update the custom ID
            $discogsReleaseCustomId = new DiscogsReleaseCustomId;
            $discogsReleaseCustomId->fill([
                'release_id' => $discogsRelease['release_id'],
                'persistent_album_id' => $album['persistent_id'],
                'artist' => $album['artist']['name'],
                'title' => $album['name'],
            ]);
            $discogsReleaseCustomId->store($discogsReleaseCustomId);
        }

        $this->render();
    }

    #[On('discogs-searched')]
    public function searched($filterValues)
    {
        $this->filterValues = $filterValues;
    }

    public function render()
    {

        $discogsRelease = new DiscogsRelease;
        $loadedDiscogsReleases = $discogsRelease->getDiscogsReleases($this->filterValues);
        $discogsReleases = $loadedDiscogsReleases->toArray()['data'];

        return view('livewire.discogs.discogs-results', [
            'filterValues' => $this->filterValues,
            'discogsReleases' => $discogsReleases,
            'loadedDiscogsReleases' => $loadedDiscogsReleases,
        ]);
    }
}
