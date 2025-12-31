<?php

namespace App\Livewire\Modals;

use App\Models\Concert\ConcertItem;
use LivewireUI\Modal\ModalComponent;

class SetlistFmModal extends ModalComponent
{
    public string $concertItemId;

    public function render()
    {

        $concertItem = ConcertItem::where('id', $this->concertItemId)->first();

        $setlistFmId = '';
        if ($concertItem?->setlistfm_url) {
            $setlistFmId = explode('-', basename($concertItem->setlistfm_url, '.html'));
            $setlistFmId = end($setlistFmId);
        }

        return view('livewire.modals.setlist-fm', compact('concertItem', 'setlistFmId'));
    }
}
