<?php

namespace App\Livewire\LastFm;

use App\Livewire\Forms\LastFm\LastFmSearchFormInit;
use App\Traits\Forms\SearchForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Livewire\Component;

class LastFmSearch extends Component implements HasForms
{
    use InteractsWithForms;
    use SearchForm;

    private LastFmSearchFormInit $searchFormInit;

    public ?array $filterValues = [];

    public array $searchFormData;

    public ?array $data = [];

    public function boot()
    {
        $this->searchFormInit = new LastFmSearchFormInit($this->searchFormData);
        $this->filterValues = $this->searchFormInit->init($this->filterValues);
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('album')
                    ->options(
                        collect($this->searchFormData['albums_filament'])
                            ->toArray()
                    )
                    ->searchable()
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(function () {
                        $this->search();
                    }),
            ])
            ->statePath('data');
    }

    public function search()
    {
        $this->filterValues['album'] = $this->data['album'] ?? null;
        $this->dispatch('last-fm-searched', $this->filterValues);
        $this->skipRender();
    }

    public function render()
    {
        return view('livewire.last-fm.last-fm-search');
    }
}
