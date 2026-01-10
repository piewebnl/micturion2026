<x-modal.modal>

    <div class="flex flex-row" x-data="{ showCropper: false }" x-cloak>


        <div class="mb-4 flex pr-4">
            @if ($album->spineImage?->slug)
                <img src="/storage/images/spines/{{ $album->spineImage->slug }}.jpg"
                    class="h-[400px] border-2 border-gray-500"" />
            @endif
        </div>
        <div>

            <x-messages.flash />

            <div class="mb-2">
                <h2 class="text-2xl"> {{ $album->artist->name }} - {{ $album->name }}</h2>
                <span class="text-zinc-500">{{ $album->year }}</span>

            </div>

            <div class="table-wrapper">
                <table class="table-layout w-full">
                    @foreach ($songs as $index => $song)
                        <tr>
                            @php
                                if ($index > 0) {
                                    $previousSong = $songs[$index - 1];
                                }

                                $discHeader = '';
                                if ($song->disc_count > 1 && $index == 0) {
                                    echo "<tr><td class='colspan='3'>Disc 1</td></tr>";
                                }

                                if ($index > 0 && $song->disc_number != $previousSong->disc_number) {
                                    echo "<tr><td  class='disc-number' colspan='3'>Disc " .
                                        $song->disc_number .
                                        '</td></tr>';
                                }
                            @endphp
                            <td class="track-number">{{ $song->track_number }}</td>
                            <td class="track-name">
                                {{ $song->name }}
                                @if ($song->album_artist)
                                    <span> - {{ $song->album_artist }}</span>
                                @endif
                                @if (Str::contains($song->grouping, ' - Bonus'))
                                    <span v-if="isBonus(song.grouping)" class="text-zinc-500">Bonus</span>
                                @endif
                            </td>
                            <td class="track-time">{{ $song->time }}</td>
                            @auth
                                <td class="track-rating">
                                    <x-rating.rating rating="{{ $song->rating }}" />
                                </td>
                                @if (isset($showLastFmScrobble) && $showLastFmScrobble)
                                    )
                                    <td>
                                        @livewire('actions.api-fetcher', [
                                            'buttonText' => 'Scrobble',
                                            'class' => 'btn-primary',
                                            'progressBarTexts' => [
                                                'fetching' => 'Busy',
                                                'done' => 'Done!',
                                            ],
                                            'url' => '/admin/last-fm-api/scrobble/track/create',
                                            'id' => 'song.' . $song->id,
                                            'data' => [['id' => $song->id]],
                                        ])
                                    </td>
                                @endif
                            @endauth
                        </tr>
                    @endforeach
                </table>
            </div>


            <div class="mb-4 mt-4">

                <button x-on:click="showCropper =! showCropper" type="button" class="btn-primary" name="show"
                    id="show">
                    Show Spine Cropper
                </button>

                @if ($album->spineImage?->slug)
                    <button wire:click="saveSpineImage('{{ $album->spineImage->id }}' )" type="button"
                        class="btn-primary" name="show" id="show">
                        Spine Image mark as Checked
                    </button>
                @endif
            </div>

            <div class="mb-4">

                <div x-show="showCropper">

                    @if ($discogsRelease?->release_id)
                        <div class="mb-2">
                            <a target="_blank"
                                href="https://www.discogs.com/release/{{ $discogsRelease->release_id }}">View
                                on
                                Discogs</a>
                        </div>
                        @foreach ($sourceImageUrls as $key => $sourceImageUrl)
                            <livewire:images.image-cropper :sourceImageUrl="$sourceImageUrl" :handler="'saveSpineImage'" :index="$key"
                                :id="$album->spineImage->id" />
                        @endforeach
                    @else
                        No
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-modal.modal>
