<x-modal.modal>

    @if ($setlistFmId)
        <div style="text-align: center;" class="setlistImage">
            <a href="{{ $concertItem->setlistfm_url }}" target="_blank">
                <img src="https://www.setlist.fm/widgets/setlist-image-v1?id={{ $setlistFmId }}&size=large"
                    alt="" /></a>
        </div>
    @else
        Something wrong...no setlist found
    @endif
</x-modal.modal>
