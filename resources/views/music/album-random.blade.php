<x-layouts.app>
    <section class="p-4">
        <header class="mb-6">
            <h1 class="text-3xl text-primary">Random Album Picker</h1>
        </header>

        @livewire('music.album-random-search', ['searchFormData' => $searchFormData])
        @livewire('music.album-random-results', ['searchFormData' => $searchFormData])


    </section>
</x-layouts.app>
