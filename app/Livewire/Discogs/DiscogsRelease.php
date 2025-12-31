<?php

namespace App\Livewire\Discogs;

use Illuminate\Support\Str;
use Livewire\Attributes\Rule;
use Livewire\Component;

class DiscogsRelease extends Component
{
    public $discogsRelease;

    #[Rule('regex:/^(https:\/\/[a-z]+\.discogs\.com\/)/')]
    public string $discogsReleaseUrl = '';

    public string $discogsReleaseId = '';

    public bool $showNotes;

    public function saveDiscogsRelease()
    {

        $validated = $this->validate();
        $releaseId = Str::before(Str::after($validated['discogsReleaseUrl'], '/release/'), '-');

        $this->dispatch(
            'discogs-results-save-release',
            $releaseId,
            $this->discogsRelease['album_id'],
        );
    }

    public function render()
    {
        return view('livewire.discogs.discogs-release', ['showNotes' => $this->showNotes]);
    }
}
