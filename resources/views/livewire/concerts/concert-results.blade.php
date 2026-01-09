<section class="p-4">

    <div wire:loading>
        <div>
            <x-load-more.spinner />
        </div>
    </div>
    <span class="mb-4 block">{{ $loadedConcerts->total() }} results</span>

    <div>
        @if ($filterValues['view'] == 'grid')
            <ul class="grid-lg">

                @foreach ($concerts as $index => $concert)
                    <li>
                        @if ($concert['concert_festival'] == true)
                            @include('livewire.concerts.concert-results-festival')
                        @else
                            @php
                                $images = [];
                            @endphp
                            @foreach ($concert['concert_items'] as $key => $concertItem)
                                @php
                                    $label = '';
                                    if ($concertItem['support']) {
                                        $label = $concertItem['concert_artist']['name'];
                                    }

                                    if ($concertItem['concert_image']['slug'] ?? false) {
                                        $images[] = [
                                            'type' => 'concert',
                                            'label' => $label,
                                            'slug' => $concertItem['concert_image']['slug'],
                                            'hash' => $concertItem['concert_image']['hash'],
                                            'largest_width' => $concertItem['concert_image']['largest_width'],
                                        ];
                                    }
                                @endphp
                            @endforeach
                            <div class="relative" style="h-[350px]">
                                <x-images.slider :images="$images" type="concert" class="relative mb-2 rounded-2xl" />
                            </div>
                            @foreach ($concert['concert_items'] as $key => $concertItem)
                                @if (!$concertItem['support'] ?? false)
                                    <h3 class="dark:text-primary-dark text-2xl font-medium text-primary">
                                        {{ $concertItem['concert_artist']['name'] }}</h3>
                                @else
                                    <h3 class="text-xl font-medium dark:text-zinc-400">
                                        {{ $concertItem['concert_artist']['name'] }}</h3>
                                @endif

                                @if ($key == 0)
                                    @if ($concert['concert_festival']['name'] ?? false)
                                        <div class="mb-4">
                                            <h2 class="text-xl text-zinc-600 dark:text-zinc-400">
                                                {{ $concert['concert_festival']['name'] }}</h2>
                                            <span
                                                class="text-zinc-500 dark:text-zinc-500">{{ $concert['concert_venue']['name'] }}
                                                -
                                                {{ date('d M Y', strtotime($concert['date'])) }}</span>
                                        </div>
                                    @else
                                        <span
                                            class="text-zinc-500 dark:text-zinc-500">{{ $concert['concert_venue']['name'] }}
                                            -
                                            {{ date('d M Y', strtotime($concert['date'])) }}</span><br />
                                    @endif
                                    @if ($concert['support'] ?? false)
                                        <span class="text-zinc-500 dark:text-zinc-500">Support for</span>
                                    @endif
                                @endif
                            @endforeach


                            @foreach ($concert['concert_items'] as $key => $concertItem)
                                @if ($concertItem['setlistfm_url'] ?? false)
                                    <div class="mb-4">
                                        <button class="btn-secondary mt-4 text-xs"
                                            wire:click="$dispatch('openModal', {
                component: 'modals.setlist-fm-modal',
                arguments: {
                    concertItemId: '{{ $concertItem['id'] }}'
                }
            })">Setlist
                                            {{ $concertItem['concert_artist']['name'] }}
                                        </button>
                                    </div>
                                @endif
                            @endforeach
                            @auth
                                <a href="{{ \App\Filament\Resources\ConcertResource::getUrl('edit', ['record' => $concert['id']]) }}">Edit</a>
                            @endauth
                        @endif

                    </li>
                @endforeach
            </ul>
        @else
            <div class="table-layout">
                <table>
                    <thead>
                        <th>Name</th>
                        <th>Support</th>
                        <th>Date</th>
                        <th>Venue name</th>
                        <th>Venue city</th>
                        <th>Venue country</th>
                        <th>Setlist FM</th>
                        @auth
                            <th>
                                Edit</th>
                        @endauth
                    </thead>
                    @foreach ($concerts as $concert)
                        <tr class="mb-4 max-w-sm">
                            <td class="text-lg">
                                @foreach ($concert['concert_items'] as $key => $concertItem)
                                    @if (!$concertItem['support'])
                                        {{ $concertItem['concert_artist']['name'] }}</h3>
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                @foreach ($concert['concert_items'] as $key => $concertItem)
                                    @if ($concertItem['support'])
                                        {{ $concertItem['concert_artist']['name'] }}</h3>
                                    @endif
                                @endforeach
                            </td>
                            <td class="whitespace-nowrap">
                                {{ date('d M Y', strtotime($concert['date'])) }}</td>
                            <td>{{ $concert['concert_venue']['name'] }}</td>
                            <td>{{ $concert['concert_venue']['city'] }}</td>
                            <td>{{ $concert['concert_venue']['country'] }}</td>
                            <td class="whitespace-nowrap">
                                @foreach ($concert['concert_items'] as $key => $concertItem)
                                    @if ($concertItem['setlistfm_url'])
                                        <a href="#"
                                            wire:click="$dispatch('openModal', {
                component: 'modals.setlist-fm-modal',
                arguments: {
                    concertItemId: '{{ $concertItem['id'] }}'
                }
            })">Setlist
                                            {{ $concertItem['concert_artist']['name'] }}
                                        </a>
                                    @endif
                                @endforeach
                            </td>
                            @auth
                                <td>
                                    <a
                                        href="{{ \App\Filament\Resources\ConcertResource::getUrl('edit', ['record' => $concert['id']]) }}">Edit</a>
                                </td>
                            @endauth
                        </tr>
                    @endforeach
                </table>
            </div>

        @endif
    </div>

    @if (!empty($loadedConcerts))
        <x-load-more.load-more :items=$loadedConcerts />
        @if ($loadedConcerts->currentPage() == $loadedConcerts->lastPage())
            <p class="p-4">No more results</p>
        @endif
    @else
        <p class="p-4">Nothing found</p>
    @endif


</section>
