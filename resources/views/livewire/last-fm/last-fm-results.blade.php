<section class="relative">

    @if ($filterValues['album'])
        @livewire(
            'actions.api-fetcher',
            [
                'id' => 'scrobble-album-all',
                'buttonText' => 'Scrobble album',
                'class' => 'btn-primary mb-2',
                'progressBarTexts' => [
                    'fetching' => 'Scrobbling ' . $album->artist->name . ' - ' . $album->name,
                    'done' => 'Scrobbled!',
                ],
                'url' => '/admin/last-fm-api/scrobble/track/create',
                'data' => $songIds['all'],
            ],
            key('scrobble-disc-all')
        )

        @if ($discCount > 1)
            <div class="mb-4 flex flex-row gap-2">
                @for ($disc = 1; $disc <= $discCount; $disc++)
                    @livewire(
                        'actions.api-fetcher',
                        [
                            'id' => 'scrobble-album-' . $disc,
                            'buttonText' => 'Scrobble disc ' . $disc,
                            'class' => 'btn-primary',
                            'progressBarTexts' => [
                                'fetching' => 'Scrobbling ' . $album->artist->name . ' - ' . $album->name,
                                'done' => 'Scrobbled!',
                            ],
                            'url' => '/admin/last-fm-api/scrobble/track/create',
                            'data' => $songIds[$disc],
                        ],
                        key('scrobble-disc-' . $disc)
                    )
                @endfor
            </div>

        @endif
    @endif


    <div class="w-full">
        @livewire('music.music-tracklist', [
            'lazy' => true,
            'albumId' => $filterValues['album'],
            key($filterValues['album']),
            'showLastFmScrobble' => true,
        ])
    </div>
</section>
