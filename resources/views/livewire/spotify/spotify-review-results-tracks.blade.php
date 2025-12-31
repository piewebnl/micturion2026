<section class="relative">

    <div class="mb-4">
        {{ $tracks->links('livewire.pagination.pagination', ['dispatchMethod' => 'spotify-review-set-page']) }}
    </div>

    <div wire:loading>
        <div>
            <x-load-more.spinner />
        </div>
    </div>

    <div class="table-wrapper mb-8">

        <table class="table-layout w-full">
            @foreach ($tracks as $index => $track)
                @livewire('spotify.spotify-track', ['spotifyTrack' => $track], key(Str::random()))
            @endforeach
        </table>
    </div>

    <div wire:loading>
        <div>
            <x-load-more.spinner />
        </div>
    </div>

    <div class="mb-4">
        {{ $tracks->links('livewire.pagination.pagination', ['dispatchMethod' => 'spotify-review-set-page']) }}
    </div>

</section>
