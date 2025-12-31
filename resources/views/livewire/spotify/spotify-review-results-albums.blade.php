<section class="relative">

    <div class="mb-4">
        {{ $albums->links('livewire.pagination.pagination', ['dispatchMethod' => 'spotify-review-set-page']) }}
    </div>

    <div wire:loading>
        <div>
            <x-load-more.spinner />
        </div>
    </div>

    <div class="table-wrapper mb-8">

        <table class="table-layout w-full">

            @foreach ($albums as $index => $album)
                @livewire('spotify.spotify-album', ['spotifyAlbum' => $album], key(Str::random()))
            @endforeach
        </table>
    </div>

    <div wire:loading>
        <div>
            <x-load-more.spinner />
        </div>
    </div>

    <div class="mb-4">
        {{ $albums->links('livewire.pagination.pagination', ['dispatchMethod' => 'spotify-review-set-page']) }}
    </div>

</section>
