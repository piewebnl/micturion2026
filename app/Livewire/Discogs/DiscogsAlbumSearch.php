<?php

namespace App\Livewire\Discogs;

use App\Models\Music\Album;
use App\Services\Forms\FormDataGenerator;
use Livewire\Component;

class DiscogsAlbumSearch extends Component
{
    public $discogsReleaseId;

    public ?int $selectedAlbumId = null;

    public string $keyword = '';

    public array $items;

    public function mount(int|string $discogsReleaseId, ?int $selectedAlbumId = null): void
    {
        $this->discogsReleaseId = $discogsReleaseId;
        $this->selectedAlbumId = $selectedAlbumId;
    }

    public function updatedKeyword(): void
    {

        $album = new Album;
        $items = $album->searchAlbumWithArtist(['keyword' => $this->keyword]);

        $formDataGenerator = new FormDataGenerator;
        $formDataGenerator->setLabel(['artist_name', 'name', 'format_name', 'category_name']);
        $formData = $formDataGenerator->generate($items);
        $this->items = $formData;
    }

    public function saveDiscogsReleaseAlbum()
    {

        $this->dispatch('discogs-results-save-discogs-release', $this->discogsReleaseId, $this->selectedAlbumId);
    }

    public function selectAlbum(string|int|null $value): void
    {
        $this->selectedAlbumId = $value ? (int) $value : null;

        // emit discogsReleaseId instead of rowId
        $this->dispatch(
            'albumSelected',
            discogsReleaseId: $this->discogsReleaseId,
            albumId: $this->selectedAlbumId
        );
    }

    public function render()
    {
        return view('livewire.discogs.discogs-album-search');
    }
}
