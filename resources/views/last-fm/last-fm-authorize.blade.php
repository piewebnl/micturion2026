<x-layouts.app>
    <section class="p-4">
        <header class="mb-6">
            <h1 class="text-3xl text-primary">Last FM Authorize</h1>
        </header>

        @if (isset($lastFmApiKey))
            <a href="http://www.last.fm/api/auth/?api_key={{ $lastFmApiKey }}" class="btn-primary">Connect</a>
        @endif


    </section>
</x-layouts.app>
