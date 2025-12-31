<section class="p-4">

    <div wire:loading>
        <div>
            <x-load-more.spinner />
        </div>
    </div>

    <div class="table-layout">
        @foreach ($tiermakerArtists as $tiermakerArtist)
            <x-tiermaker.tiermaker :tiermakerArtist="$tiermakerArtist" :labels="$labels" />
        @endforeach
    </div>

</section>
