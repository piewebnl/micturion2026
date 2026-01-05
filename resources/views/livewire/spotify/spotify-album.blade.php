@php
    $bgClass = '';
    if ($spotifyAlbum->status == 'error') {
        $bgClass = 'bg-error';
        $textClass = 'text-error';
    }
    if ($spotifyAlbum->status == 'success') {
        $bgClass = 'bg-success';
        $textClass = 'text-success';
    }
@endphp
<tr>
    <td class="{{ $bgClass }} "></td>
    <td>
        <x-images.image type="album" slug="{{ $spotifyAlbum->album_image_slug }}" size="75"
            largestWidth="{{ $spotifyAlbum->album_image_largest_width }}" class="cursor-pointer"
            hash="{{ $spotifyAlbum->album_image_hash }}" alt="{{ $spotifyAlbum->name }} Album Cover" />
    </td>

    <td class="min-w-32">{{ $spotifyAlbum->artist_name }}
        @if ($spotifyAlbum->spotify_album_artist != '')
            <br /><a href="https://open.spotify.com/album/{{ $spotifyAlbum->spotify_album_spotify_api_album_id }}"
                target="_blank">{{ $spotifyAlbum->spotify_album_artist }}</a>
        @endif

    </td>
    <td class="min-w-32">{{ $spotifyAlbum->album_name }}
        @if ($spotifyAlbum->spotify_album_name != '')
            <br /><a href="https://open.spotify.com/album/{{ $spotifyAlbum->spotify_album_spotify_api_album_id }}"
                target="_blank">{{ $spotifyAlbum->spotify_album_name }}</a>
        @endif
    </td>

    <td>

        <div class="relative mx-5 my-10">
            <div
                class="to-grreen-500 relative mb-4 flex h-2 w-[110px] overflow-hidden rounded bg-gray-500 bg-gradient-to-r from-red-800 via-yellow-800 to-green-800 text-xs">
                <div style="width: 10px; height: 100%; left: {{ $spotifyAlbum->score }}px"
                    class="absolute border border-gray-700 bg-gray-300">
                </div>
            </div>
            @if ($spotifyAlbum->status == 'custom')
                <div class="mb-2 flex w-[110px] justify-center text-xs">
                    <strong class="{{ $textClass }}">Custom match</strong>
                </div>
            @endif
            @if ($spotifyAlbum->score == 'unavailable')
                <div class="mb-2 flex w-[110px] justify-center text-xs">
                    <strong class="{{ $textClass }}">Marked not available</strong>
                </div>
            @endif
            @if ($spotifyAlbum->spotify_album_spotify_api_album_id == null)
                No spotify match
            @endif
        </div>
    </td>
    <td>
        @if ($spotifyAlbum->spotify_album_spotify_api_album_id)
            <button class="btn-outline text-red-500"
                wire:click="$dispatch('spotify-review-results-change-album-status', { spotifyAlbumId: '{{ $spotifyAlbum->id }}', status: 'error' })">
                <x-icons.thumbs-down width="32" height="32" /></button>
        @endif

    </td>
    <td>
        @if ($spotifyAlbum->spotify_album_spotify_api_album_id)
            <button class="btn-outline text-green-500"
                wire:click="$dispatch('spotify-review-results-change-album-status', { spotifyAlbumId: '{{ $spotifyAlbum->id }}', status: 'success' })">
                <x-icons.thumbs-up width="32" height="32" /></button>
        @endif
    </td>
    <td class="min-w-40">
        <form wire:submit.prevent="submit" x-data="{ submitForm() { this.$refs.submitButton.click(); } }" class="max-w-lg">
            @csrf
            <x-forms.input :wireModel="'customIdUrl'" id="custom-id-url" placeholder="" name="text" type="text"
                label="URL for custom ID" class="mb-6" wire:change="submitForm">
            </x-forms.input>

            <x-buttons.button wire:click="submitForm" class="btn-secondary">Save</x-buttons.button>

        </form>
    </td>
</tr>
