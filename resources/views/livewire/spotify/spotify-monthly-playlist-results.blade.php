<section class="relative">

    <div wire:loading>
        <div>
            <x-load-more.spinner />
        </div>
    </div>

    @if ($playlists->count() == 0)
        No playlists found.
    @else
        @if ($filterValues['view'] == 'grid')

            @foreach ($playlists as $index => $playlist)
                @if ($index == 0 || ($index > 0 && $playlist->name != $playlists[$index - 1]->name))
                    @if ($index != 0)
                        </div>
                    @endif
                    <h3
                        class="mb-2 mt-4 bg-gradient-to-r from-orange-500 via-amber-600 to-amber-700 bg-clip-text text-2xl text-transparent dark:bg-gradient-to-r dark:from-orange-500 dark:via-amber-500 dark:to-amber-400">
                        {{ $playlist->name }}
                    </h3>
                    @php
                        $count = 0;
                    @endphp
                @endif
                @if ($count == 0)
                    <div class="lg:columns-2 xl:columns-3">
                @endif
                @php
                    $count++;
                @endphp
                <div class="mb-2 flex flex-row gap-2">
                    <div class="nowrap">
                        <img src="{{ $playlist->spotify_playlist_track_artwork_url }}" class="w-[75px] max-w-none" />
                    </div>
                    <div class="w-8 py-2 text-right">{{ $count }}.</div>
                    <div class="py-2 pr-2">
                        <div>

                            <span class="text-lg">{{ $playlist->spotify_playlist_track_name }}</span> -
                            <span>{{ $playlist->spotify_playlist_track_artist }}</span>
                        </div>
                        <div class="text-zinc-500">{{ $playlist->spotify_playlist_track_album }}</div>
                    </div>
                </div>
            @endforeach
            @if ($count > 0)
                </div>
            @endif
        @else
            <div class="table-wrapper mb-8">

                <table class="table-layout w-full">
                    @foreach ($playlists as $index => $playlist)
                        @if ($index == 0 || ($index > 0 && $playlist->name != $playlists[$index - 1]->name))
                            <tr>
                                <td colspan="3"
                                    class="mb-2 mt-4 bg-gradient-to-r from-orange-500 via-amber-600 to-amber-700 bg-clip-text text-2xl text-transparent dark:bg-gradient-to-r dark:from-orange-500 dark:via-amber-500 dark:to-amber-400">
                                    {{ $playlist->name }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td class="w-1/3 text-lg">{{ $playlist->spotify_playlist_track_name }}</td>
                            <td class="w-1/3">{{ $playlist->artist }}</td>
                            <td class="w-1/3">{{ $playlist->spotify_playlist_track_album }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif

        @if (!empty($playlists))
            <x-load-more.load-more :items="$playlists" />
            @if ($playlists->currentPage() == $playlists->lastPage())
                <p class="p-4">No more results</p>
            @endif
        @else
            <p class="p-4">Nothing found</p>
        @endif


    @endif


</section>
