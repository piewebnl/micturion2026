@php
    $bgClass = '';
    $textClass = '';
    if ($spotifyTrack->status == 'error') {
        $bgClass = 'bg-error';
        $textClass = 'text-error';
    }
    if ($spotifyTrack->status == 'success') {
        $bgClass = 'bg-success';
        $textClass = 'text-success';
    }
@endphp
<tr>
    <td class="{{ $bgClass }} "></td>
    <td>
        <x-images.image type="album" slug="{{ $spotifyTrack->album_image_slug }}" size="75"
            largestWidth="{{ $spotifyTrack->album_image_largest_width }}" class="min-w-[50px] cursor-pointer"
            hash="{{ $spotifyTrack->album_image_hash }}"
            alt="{{ $spotifyTrack->name }} - {{ $spotifyTrack->album_name }} Album Cover" />
    </td>
    <td class="min-w-32">{{ $spotifyTrack->artist_name }}
        @if ($spotifyTrack->spotify_track_artist != '')
            <br /><a href="https://open.spotify.com/track/{{ $spotifyTrack->spotify_track_spotify_api_track_id }}"
                target="_blank">{{ $spotifyTrack->spotify_track_artist }}</a>
        @endif

    </td>

    <td class="min-w-32">{{ $spotifyTrack->album_name }}
        @if ($spotifyTrack->spotify_track_album != '')
            <br /><a href="https://open.spotify.com/album/{{ $spotifyTrack->spotify_track_spotify_api_album_id }}"
                target="_blank">{{ $spotifyTrack->spotify_track_album }}</a>
        @endif
    </td>
    <td class="min-w-64">{{ $spotifyTrack->song_track_number }}. {{ $spotifyTrack->song_name }}
        @if ($spotifyTrack->spotify_track_name != '')
            <br />
            <a href="https://open.spotify.com/track/{{ $spotifyTrack->spotify_track_spotify_api_track_id }}"
                target="_blank">{{ $spotifyTrack->spotify_track_number }}. {{ $spotifyTrack->spotify_track_name }}</a>
            </a>
        @endif
    </td>
    <td>{{ $spotifyTrack->status }} / {{ $spotifyTrack->score }}</td>
    <td>
        <div class="relative mx-5 my-10">
            <div class="relative mx-5 my-10">
                <div
                    class="to-grreen-500 relative mb-4 flex h-2 w-[110px] overflow-hidden rounded bg-gray-500 bg-gradient-to-r from-red-800 via-yellow-800 to-green-800 text-xs">
                    <div style="width: 10px; height: 100%; left: {{ $spotifyTrack->score }}px"
                        class="absolute border border-gray-700 bg-gray-300">
                    </div>
                </div>
                @if ($spotifyTrack->status == 'custom')
                    <div class="mb-2 flex w-[110px] justify-center text-xs">
                        <strong class="{{ $textClass }}">Custom match</strong>
                    </div>
                @endif
                @if ($spotifyTrack->score == 'unavailable')
                    <div class="mb-2 flex w-[110px] justify-center text-xs">
                        <strong class="{{ $textClass }}">Marked not available</strong>
                    </div>
                @endif
                @if ($spotifyTrack->spotify_album_spotify_api_album_id == null)
                    No spotify match
                @endif
            </div>
        </div>
    </td>
    <td>
        <button class="btn-outline text-red-500"
            wire:click="$dispatch('spotify-review-results-change-track-status', { spotifyTrackId: '{{ $spotifyTrack->id }}', status: 'error' })">
            <x-icons.thumbs-down width="32" height="32" /></button>
    </td>
    <td>
        @if ($spotifyTrack->spotify_track_spotify_api_track_id)
            <button class="btn-outline text-green-500"
                wire:click="$dispatch('spotify-review-results-change-track-status', { spotifyTrackId: '{{ $spotifyTrack->id }}', status: 'success' })">
                <x-icons.thumbs-up width="32" height="32" /></button>

            <button class="btn-outline ml-4"
                wire:click="$dispatch('spotify-review-results-delete-track', { spotifyTrackId: '{{ $spotifyTrack->id }}' })">
                <x-icons.close width="32" height="32" /></button>
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
