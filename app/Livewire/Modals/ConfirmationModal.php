<?php

namespace App\Livewire\Modals;

use LivewireUI\Modal\ModalComponent;

class ConfirmationModal extends ModalComponent
{
    public string $dispatch; // dispatch { $modelname }-{ $method }  like: meal-delete

    public string $method = 'delete';

    public string $title = 'Confirm?';

    public function confirm()
    {

        $this->dispatch($this->dispatch);
        $this->closeModal();
    }

    public function decline()
    {
        $this->closeModal();
    }

    public function render()
    {
        if ($this->method == 'delete') {

            $modal = [
                'title' => $this->title,
                'message' => null,
                'confirm_text' => 'Yes, delete',
                'decline_text' => 'Decline',
            ];
        }

        return view('livewire.modals.confirmation-modal', compact('modal'));
    }
}
