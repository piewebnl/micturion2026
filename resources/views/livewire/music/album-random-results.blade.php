@props(['displayArtist' => true])


<section class="relative sm:p-4">

    <div wire:loading>
        <div>
            <x-load-more.spinner />
        </div>
    </div>

    <div class="grid-md">

        @foreach ($artists as $index => $artist)
            <div class="max-w-[150px] md:max-w-[300px]">
                <h2
                    class="mb-2 bg-gradient-to-r from-orange-500 via-amber-600 to-amber-700 bg-clip-text text-2xl text-transparent dark:bg-gradient-to-r dark:from-orange-500 dark:via-amber-500 dark:to-amber-400">
                    {{ $artist->name }}
                </h2>
                <x-images.image type="album" slug="{{ $artist->album_image_slug }}" size="300"
                    largestWidth="{{ $artist->album_image_largest_width }}" hash="{{ $artist->album_image_hash }}"
                    class="w-full cursor-pointer" alt="{{ $artist->name }} - {{ $artist->album_name }} Album Cover"
                    wire:click="$dispatch('openModal', {
                component: 'modals.music-tracklist-modal',
                arguments: {
                    albumId: '{{ $artist->album_id }}'
                }
            })" />

                <div class="mt-1 p-1">
                    <span class="block">{{ $artist->album_name }}</span>
                    @if ($artist->format_name != 'None')
                        <span class="text-zinc-600">{{ $artist->format_name }}</span>

                        @if ($artist->subformat_name)
                            <span class="text-sm text-zinc-400">({{ $artist->subformat_name }})</span>
                        @endif
                    @endif
                </div>
                <button wire:click="remove({{ $artist->album_id }})" type="button" class="btn" name="clear"
                    id="remove">
                    <x-icons.close />Nah
                </button>
            </div>
        @endforeach
    </div>

</section>
