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
                        echo "<tr><td  class='disc-number' colspan='3'>Disc " . $song->disc_number . '</td></tr>';
                    }
                @endphp
                <td class="track-number">{{ $song->track_number }}</td>
                <td class="track-name">
                    {{ $song->name }}
                    @if ($song->album_artist != $song->artist)
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
                    @if ($showLastFmScrobble)
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
