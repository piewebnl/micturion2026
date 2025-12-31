<section class="py-4" x-data="{ showFound: false, showNotFound: false }" x-cloak>


    @if ($spotifyPlaylist)

        <a
            href="{{ route('spotify.download-playlist', ['filename' => 'rutger_debbie.m3u8', 't' => now()->timestamp]) }}">
            Download
        </a>


        <button x-on:click="showFound =! showFound" type="button" class="btn-primary" name="show" id="show">
            Show found
        </button>

        <button x-on:click="showNotFound =! showNotFound" type="button" class="btn-primary" name="show"
            id="show">
            Show not found
        </button>


        @if (!empty($spotifyPlaylist->spotifyPlaylistTracksWithSong[0]))
            <div x-show="showFound">
                <h2 class="mb-2 text-xl text-zinc-600 dark:text-zinc-400">Found with song</h2>
                <div class="flex flex-row">
                    <div class="table-wrapper mb-8">
                        <table class="table-layout w-full">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Spotify track Name</th>
                                    <th>Song</th>
                                    <th>Album</th>
                                    <th>Artist</th>
                                </tr>
                            </thead>
                            @foreach ($spotifyPlaylist->spotifyPlaylistTracksWithSong as $spotifyPlaylistTrack)
                                <tr>
                                    <td class="nowrap">
                                        <img src="{{ $spotifyPlaylistTrack->artwork_url }}"
                                            class="w-[75px] max-w-none" />
                                    </td>
                                    <td>{{ $spotifyPlaylistTrack->spotify_api_track_id }}</td>
                                    <td>{{ $spotifyPlaylistTrack->name }}</td>
                                    <td>{{ $spotifyPlaylistTrack->song?->name }}</td>
                                    <td>{{ $spotifyPlaylistTrack->song?->album?->name }}</td>
                                    <td>{{ $spotifyPlaylistTrack->song?->album?->artist?->name }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        @endif

        @if (!empty($spotifyPlaylist->spotifyPlaylistTracksWithoutSong[0])))
            <div x-show="showNotFound">
                <h2 class="mb-2 text-xl text-zinc-600 dark:text-zinc-400">Not found with song</h2>
                <div class="flex flex-row">
                    <div class="table-wrapper mb-8">
                        <table class="table-layout w-full">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Spotify track Name</th>

                                </tr>
                            </thead>
                            @foreach ($spotifyPlaylist->spotifyPlaylistTracksWithoutSong as $spotifyPlaylistTrack)
                                <tr>
                                    <td class="nowrap">
                                        <img src="{{ $spotifyPlaylistTrack->artwork_url }}"
                                            class="w-[75px] max-w-none" />
                                    </td>
                                    <td>{{ $spotifyPlaylistTrack->spotify_api_track_id }}</td>
                                    <td>{{ $spotifyPlaylistTrack->name }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @endif
</section>
