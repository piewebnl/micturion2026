@props(['displayArtist' => true])

@php
    $letter = '';
@endphp


<section class="relative">

    <div>
        <div wire:loading>
            <div>
                <x-load-more.spinner />
            </div>
        </div>
        <span class="mb-4 block">{{ $loadedArtists->total() }} results</span>


        @if ($filterValues['view'] == 'grid')

            <div class="grid-albums">
                @foreach ($artists as $index => $artist)
                    @if (
                        ($index == 0 && $displayArtist) ||
                            ($index > 0 && $artist['name'] != $artists[$index - 1]['name'] && $displayArtist))
                        <div class="album-grid-item artist flex max-w-[200px] cursor-pointer items-baseline p-2">
                            <div
                                class="circle relative w-full rounded-full border border-zinc-300 bg-zinc-200 pb-[100%] dark:border-zinc-700 dark:bg-zinc-800">

                                <h2
                                    class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-gradient-to-r from-orange-500 via-amber-600 to-amber-700 bg-clip-text text-2xl text-transparent dark:bg-gradient-to-r dark:from-orange-500 dark:via-amber-500 dark:to-amber-400">
                                    <button class="text-left"
                                        wire:click="$dispatch('music-search-set-filter', { field: 'artist', value: '{{ $artist['id'] }}' })">{{ $artist['name'] }}</button>
                                    @auth
                                        @if ($artist['tiermaker_artist_id'])
                                            <a href="/tiermaker/{{ $artist['tiermaker_artist_id'] }}"><x-icons.tiermaker
                                                    class="mt-2 h-6 w-6 text-zinc-400" /></a>
                                        @endif
                                    @endauth

                                </h2>

                            </div>
                        </div>
                    @endif

                    <div class="flex max-w-[150px] flex-col">

                        <x-images.image type="{{ $artist['category_image_type'] }}"
                            slug="{{ $artist['album_image_slug'] }}"
                            wire:click="$dispatch('openModal', {
                component: 'modals.music-tracklist-modal',
                arguments: {
                    albumId: '{{ $artist['album_id'] }}'
                }
            })"
                            size="150" largestWidth="{{ $artist['album_image_largest_width'] }}"
                            class="w-full cursor-pointer" hash="{{ $artist['album_image_hash'] }}"
                            alt="{{ $artist['name'] }} - {{ $artist['album_name'] }} Album Cover" />
                        <div class="mt-1 p-1">
                            <span class="block">{{ $artist['album_name'] }}</span>
                            @if ($artist['format_name'] != 'None')
                                <span class="text-zinc-600">{{ $artist['format_name'] }}</span>

                                @if ($artist['subformat_name'])
                                    <span class="text-sm text-zinc-400">({{ $artist['subformat_name'] }})</span>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @elseif ($filterValues['view'] == 'list')
            @foreach ($artists as $index => $artist)
                @if (
                    ($index == 0 && $displayArtist) ||
                        ($index > 0 && $artist['name'] != $artists[$index - 1]['name'] && $displayArtist))
                    <div class="my-8">
                        @if ($artist['sort_name'][0] != $letter)
                            <a name="{{ strtolower($artist['sort_name'][0]) }}"></a>
                    </div>
                @endif
                <a wire:click="$dispatch('music-search-set-filter', { field: 'artist', value: '{{ $artist['id'] }}' })"
                    class="w-full">
                    <h2
                        class="bg-gradient-to-r from-orange-500 via-amber-600 to-amber-700 bg-clip-text text-4xl text-transparent dark:bg-gradient-to-r dark:from-orange-500 dark:via-amber-500 dark:to-amber-400">
                        {{ $artist['name'] }}
                    </h2>
                </a>
    </div>
    @endif
    <div class="mb-6 sm:flex sm:flex-row sm:flex-wrap lg:grid lg:grid-cols-[1fr_1fr_2fr]">
        <x-images.image type="{{ $artist['category_image_type'] }}" slug="{{ $artist['album_image_slug'] }}"
            size="500" largestWidth="{{ $artist['album_image_largest_width'] }}"
            hash="{{ $artist['album_image_hash'] }}" class="max-w-[250px] sm:mb-4 sm:max-w-[300px]"
            alt="{{ $artist['name'] }} - {{ $artist['album_name'] }} Album Cover" />

        <div class="flex w-full flex-col p-4 sm:w-1/2 lg:w-full">
            <span class="text-xl">
                {{ $artist['album_name'] }}</span>
            @if ($artist['format_name'] != 'None')
                <span class="dark:text-zinc-300">{{ $artist['format_name'] }}</span>
            @endif
            <span class="dark:text-zinc-500">{{ $artist['subformat_name'] }}</span>
            <div class="text-zinc-500">
                {{ rtrim($artist['category_name'], 's') }}
            </div>
            <div class="text-zinc-500">{{ $artist['genre_name'] }}
            </div>
            @auth
                <div class="mt-4">
                    @if ($artist['album_notes'])
                        Notes:
                        {{ $artist['album_notes'] }}<br />
                    @endif

                </div>
                <div class="mt-4">
                    Play count:
                    @if ($artist['album_play_count'])
                        {{ $artist['album_play_count'] }}
                    @endif
                </div>
                <div class="mt-4">
                    Discogs:<br />
                    @foreach ($artist['discogs_releases'] as $release)
                        @if ($release['url'])
                            <a href="{{ $release['url'] }}" target="_blank">{{ $release['artist'] }} -
                                {{ $release['title'] }}</a>
                        @else
                            {{ $release['artist'] }} -
                            {{ $release['title'] }}
                        @endif
                        <br />!
                        {{ $release['notes'] }}<br />
                    @endforeach

                </div>
            @endauth
        </div>
        <div class="w-full">
            @livewire('music.music-tracklist', ['lazy' => true, 'albumId' => $artist['album_id']], key($artist['album_id']))
            </<div>
        </div>
    </div>
    @endforeach
@elseif ($filterValues['view'] == 'table')
    <div class="table-layout">
        <table>
            <thead>
                <th>Artist</th>
                <th>Album</th>
                <th>Year</th>
                <th>Category</th>
                <th>Format</th>
                <th>Subformat</th>
                <th>Notes</th>
                <th>Genre</th>
                @auth
                    <th>
                        Owned
                    </th>
                    <th>Wishlist</th>
                    <th>
                        Album play count
                    </th>
                    <th>
                        Discogs
                    </th>
                    <th>
                        Album spotify status
                    </th>
                @endauth
            </thead>
            @foreach ($artists as $artist)
                <tr class="mb-4 max-w-sm">
                    <td class="text-lg">
                        <a
                            wire:click="$dispatch('music-search-set-filter', { field: 'artist', value: '{{ $artist['id'] }}' })">{{ $artist['name'] }}</a>
                    </td>
                    <td>{{ $artist['album_name'] }}</td>
                    <td><a
                            wire:click="$dispatch('music-search-set-filter', { field: 'keyword', value: '{{ $artist['album_year'] }}' })">{{ $artist['album_year'] }}</a>
                    </td>
                    <td>{{ $artist['category_name'] }}</a>
                    </td>
                    <td>{{ $artist['format_name'] }}</td>
                    <td>{{ $artist['subformat_name'] }}</td>
                    <td>
                        @if ($artist['album_notes'])
                            {{ $artist['album_notes'] }}
                        @endif
                    </td>
                    <td>{{ $artist['genre_name'] }}</td>
                    @auth
                        <td>
                            @if ($artist['format_name'] != 'None')
                                Owned
                            @endif

                        </td>
                        <td>
                            <button class="btn"
                                wire:click="addToWishlist('{{ $artist['album_persistent_id'] }}'    )">Add
                                to
                                wishlist</button>
                        </td>
                        <td>
                            @if ($artist['album_play_count'])
                                {{ $artist['album_play_count'] }}
                            @endif
                        </td>
                        <td>
                            @foreach ($artist['discogs_releases'] as $release)
                                @if ($release['url'])
                                    <a href="{{ $release['url'] }}" target="_blank">{{ $release['artist'] }} -
                                        {{ $release['title'] }}</a>
                                @else
                                    {{ $release['artist'] }} -
                                    {{ $release['title'] }}
                                @endif
                                <br />
                            @endforeach
                        </td>
                        <td>
                            {{ $artist['album_spotify_album_status'] }}
                        </td>

                    @endauth
                </tr>
            @endforeach
        </table>
    </div>
    @endif
    @if ($filterValues['view'] == 'spines')
        @if (empty($artists))
            No spines
        @else
            <div class="flex flex-wrap gap-1">
                @foreach ($artists as $index => $artist)
                    @if ($artist['spine_image_slug'])
                        <div class="flex flex-col flex-wrap">
                            <img src="/storage/images/spines/{{ $artist['spine_image_slug'] }}.jpg"
                                class="h-[400px] border-2 border-gray-500"
                                wire:click="$dispatch('openModal', {
                component: 'modals.music-tracklist-modal',
                arguments: {
                    albumId: '{{ $artist['album_id'] }}'
                }
            })" />
                            @auth
                                <div class="text-center"> {{ $artist['spine_image_checked'] }}</div>
                            @endauth
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    @endif
    </div>

    @if (!empty($loadedArtists))
        <x-load-more.load-more :items=$loadedArtists />
        @if ($loadedArtists->currentPage() == $loadedArtists->lastPage())
            <p class="p-4">No more results</p>
        @endif
    @else
        <p class="p-4">Nothing found</p>
    @endif


</section>
