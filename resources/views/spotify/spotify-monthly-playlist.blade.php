<x-layouts.app>
    <section class="p-4">
        <header class="mb-6">
            <h1 class="text-3xl text-primary">Spotify Monthly Playlists</h1>
        </header>


        @livewire('spotify.spotify-monthly-playlist-search', ['searchFormData' => $searchFormData])
        @livewire('spotify.spotify-monthly-playlist-results', ['searchFormData' => $searchFormData])


    </section>
</x-layouts.app>
