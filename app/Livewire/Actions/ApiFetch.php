<?php

namespace App\Livewire\Actions;

use Livewire\Component;

class ApiFetch extends Component
{
    public string $id;

    public function render()
    {
        return view('livewire.actions.api-fetch');
    }
}
