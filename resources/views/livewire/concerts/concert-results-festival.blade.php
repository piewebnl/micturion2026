<div>

    @if (isset($concert['concert_festival']['concert_festival_image']['slug']))
        <x-images.image type="concert_festival"
            slug="{{ $concert['concert_festival']['concert_festival_image']['slug'] }}" size="500"
            largestWidth="{{ $concert['concert_festival']['concert_festival_image']['largest_width'] }}"
            class="mb-2 rounded-full p-6 opacity-50"
            hash="{{ $concert['concert_festival']['concert_festival_image']['hash'] }}" alt="" />
    @endif
    <h3 class="text-2xl text-primary">{{ $concert['concert_festival']['name'] }}</h3>
    <span class="text-zinc-500 dark:text-zinc-500">{{ $concert['concert_venue']['name'] }}
        -
        {{ date('d M Y', strtotime($concert['date'])) }}</span>


    @foreach ($concert['concert_items'] as $key => $concertItem)
        <h3 class="text-xl font-medium dark:text-zinc-400">
            {{ $concertItem['concert_artist']['name'] }}
        </h3>

        @if ($concertItem['setlistfm_url'])
            <button class="btn-secondary mt-4 text-xs"
                wire:click="$dispatch('openModal', {
                component: 'modals.setlist-fm-modal',
                arguments: {
                    concertId: '{{ $concertItem['id'] }}'
                }
            })">Setlist
            </button>
        @endif
    @endforeach


    @auth
        <a href="{{ \App\Filament\Resources\ConcertResource::getUrl('edit', ['record' => $concert['id']]) }}">Edit</a>
    @endauth
</div>
