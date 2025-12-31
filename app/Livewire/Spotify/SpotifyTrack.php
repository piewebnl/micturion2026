<?php

namespace App\Livewire\Spotify;

use Illuminate\Support\Str;
use Livewire\Attributes\Rule;
use Livewire\Component;

class SpotifyTrack extends Component
{
    public $spotifyTrack;

    #[Rule('regex:/^(https:\/\/[a-z]+\.spotify\.com\/)/')]
    public ?string $customIdUrl;

    public function submitForm()
    {
        $validated = $this->validate();
        $id = Str::between($validated['customIdUrl'], '/track/', '?', 'name');

        $this->dispatch(
            'spotify-review-results-save-track-custom-id-url',
            $id,
            $this->spotifyTrack['song_id'],
        );
    }

    public function render()
    {
        return view('livewire.spotify.spotify-track');
    }
}
