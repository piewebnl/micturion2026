<h2 class="py-2 text-2xl">{{ $tiermakerArtist->artist_name }}</h2>
<div class="flex flex-col gap-1">
    @foreach ($labels as $i => $label)
        <div class="flex flex-row gap-1 bg-zinc-800">
            <div
                class="text-gray-800 pointer-events-none w-[100px] h-[100px] flex items-center justify-center bg-tier-{{ strtolower($label) }}">
                {{ $label }}</div>
            <div class="flex flex-row items-center" x-ref="col-{{ $label }}">
                @foreach ($tiermakerArtist['tiermakerAlbums'] as $key => $tiermakerAlbum)
                    @if ($tiermakerAlbum['tier'] == $label)
                        @php
                            $albumImage = $tiermakerAlbum['album']->albumImage;
                        @endphp
                        <div class="z-10 shrink-0">
                            @if (isset($albumImage['slug']))
                                <x-images.image type="album" slug="{{ $albumImage['slug'] }}" size="100"
                                    largestWidth="{{ $albumImage['largest_width'] }}" hash="{{ $albumImage['hash'] }}"
                                    class="cursor-pointer" />
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endforeach
</div>
