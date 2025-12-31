@php
    $bgClass = '';
    if ($discogsRelease['discogs_release_score'] < 80) {
        $bgClass = 'bg-error';
        $textClass = 'text-error';
    }
    if ($discogsRelease['discogs_release_score'] >= 80) {
        $bgClass = 'bg-success';
        $textClass = 'text-success';
    }
@endphp
<tr x-data="{ showMore: false }" x-cloak>
    <td class="{{ $bgClass }} ">
    </td>
    <td class="w-[50px]">
        <img src="{{ $discogsRelease['discogs_release_artwork_url'] }}" class="w-[50px]" />
    </td>
    <td>{{ $discogsRelease['discogs_release_artist'] }}</td>
    <td>{{ $discogsRelease['discogs_release_title'] }}</td>
    <td>{{ $discogsRelease['discogs_release_format'] }}</td>
    <td nowrap class="text-sm">
        @if ($discogsRelease['discogs_release_date'] != '')
            {{ substr($discogsRelease['discogs_release_date'], 0, 4) }}
        @endif
    </td>
    <td>{{ $discogsRelease['discogs_release_country'] }}</td>
    <td>
        {{ $discogsRelease['discogs_release_score'] }}
    </td>

    <td class="w-[50px]">
        @if ($discogsRelease['album_image_slug'])
            <x-images.image type="{{ $discogsRelease['category_image_type'] }}"
                slug="{{ $discogsRelease['album_image_slug'] }}" size="50"
                largestWidth="{{ $discogsRelease['album_image_largest_width'] }}" class="w-[50px]"
                hash="{{ $discogsRelease['album_image_hash'] }}"
                alt="{{ $discogsRelease['artist_name'] }} - {{ $discogsRelease['album_name'] }} Album Cover" />
        @endif
    </td>
    <td>{{ $discogsRelease['artist_name'] }} </td>
    <td>
        {{ $discogsRelease['album_name'] }}<br />
    </td>
    <td class="min-w-40">

        <livewire:discogs.discogs-album-search :discogs-release-id="$discogsRelease['discogs_release_id']" :selected-album-id="$discogsRelease['album_id']"
            :wire:key="'discogs-album-search-'.$discogsRelease['discogs_release_id']" />



        <button class="btn" x-on:click="showMore =! showMore">Show more</button>

        <div x-show="showMore">
            {!! nl2br($discogsRelease['discogs_release_notes']) !!}
        </div>
    </td>
</tr>
