<?php

namespace App\Livewire\Spotify;

use Illuminate\Support\Str;
use Livewire\Attributes\Rule;
use Livewire\Component;

class SpotifyAlbum extends Component
{
    public $spotifyAlbum;

    #[Rule('regex:/^(https:\/\/[a-z]+\.spotify\.com\/)/')]
    public ?string $customIdUrl;

    public function submitForm()
    {
        $validated = $this->validate();
        $id = Str::between($validated['customIdUrl'], '/album/', '?', 'name');
        $this->dispatch(
            'spotify-review-results-save-album-custom-id-url',
            $id,
            $this->spotifyAlbum['album_id'],
        );
    }

    public function render()
    {
        return view('livewire.spotify.spotify-album');
    }
}
