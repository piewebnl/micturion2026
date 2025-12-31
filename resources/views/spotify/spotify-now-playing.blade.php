<x-layouts.app>
    <section class="p-4">
        <header class="mb-6">
            <h1 class="text-3xl text-primary">Now playing</h1>
        </header>


        @if ($currentTrack?->item)
            <div class="flex flex-row gap-2">
                <div>
                    <img src="{{ $currentTrack->item->album->images[0]->url }}" class="w-[250px]" />
                </div>
                <div class="flex flex-col p-2">
                    <span class="text-lg">{{ $currentTrack->item->name }}</span>
                    <span>{{ $currentTrack->item->artists[0]->name }}</span>
                    <span class="text-zinc-500">{{ $currentTrack->item->album->name }}</span>
                </div>
            </div>
        @endif

    </section>
</x-layouts.app>
