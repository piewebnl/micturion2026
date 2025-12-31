<?php

namespace App\Livewire\Actions;

use Livewire\Attributes\On;
use Livewire\Component;

class ApiFetcher extends Component
{
    public string $buttonText = 'Fetch all';

    public array $progressBarTexts = [];

    public string $class = 'btn';

    public string $url = '';

    public array $data = [];

    public string $token;

    public string $id = '';

    public array $item;

    public // $responses = [];

    public $total = 0;

    public $doing = 0;

    public $percentage = 0;

    public function mount()
    {
        $this->total = count($this->data);
        $this->token = @csrf_token();

        if (!$this->progressBarTexts) {
            $this->progressBarTexts = [
                'done' => 'Done',
                'fetching' => 'Fetching',
            ];
        }
    }

    #[On('api-fetch-update-response-{id}')]
    public function fetchAll()
    {
        $this->doing = $this->doing + 1;

        if ($this->doing <= $this->total) {
            $this->item = $this->data[$this->doing - 1];
            $this->dispatch('api-fetch-run-' . $this->id, ['url' => $this->url, 'data' => $this->item, 'token' => $this->token]);
            sleep(1);
        }

        $this->percentage = ceil((100 / $this->total) * ($this->doing - 1));

        if ($this->doing > $this->total) {
            $this->doing = 0;
        }
    }

    public function render()
    {

        return view('livewire.actions.api-fetcher');
    }
}
