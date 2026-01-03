<x-layouts.app>

    WEE

    <section class="mb-24 p-4" <div x-data x-init="Livewire.hook('commit', () => {
        const el = document.getElementById('c');
        if (el) {
            el.scrollIntoView({ behavior: 'smooth' });
        }
    });">

        @if (isset($pageTitle))
            <header class="mb-6">
                <h1 class="text-3xl text-primary">{{ $pageTitle }}</h1>
            </header>
        @endif

        @foreach ($livewireComponents as $livewireComponent)
            @livewire($livewireComponent['name'], [
                'id' => $livewireComponent['id'] ?? null,
                'method' => $livewireComponent['method'] ?? null,
                'searchFormData' => $livewireComponent['searchFormData'] ?? null,
            ])
        @endforEach


    </section>
</x-layouts.app>
