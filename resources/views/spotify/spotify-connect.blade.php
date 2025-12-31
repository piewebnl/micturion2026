<x-layouts.app>
    <section class="p-4">
        <header class="mb-6">
            <h1 class="text-3xl text-primary">Spotif Connect</h1>
        </header>

        @if (isset($authUrl))
            <a href="{{ $authUrl }}" class="btn-primary">Connect</a>
        @endif

    </section>
</x-layouts.app>
