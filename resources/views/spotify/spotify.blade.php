<x-layouts.app>
    <section class="p-4">
        <header class="mb-6">
            <h1 class="text-3xl text-primary">Spotify</h1>
        </header>


        @livewire('spotify.spotify-itunes-playlist-csv')

        @livewire('spotify.spotify-review-search', ['searchFormData' => $searchFormData])
        @livewire('spotify.spotify-review-results', ['searchFormData' => $searchFormData])

    </section>
</x-layouts.app>
