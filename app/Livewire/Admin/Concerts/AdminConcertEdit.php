<?php

namespace App\Livewire\Admin\Concerts;

use App\Models\Concert\ConcertArtist;
use App\Models\Concert\ConcertFestival;
use App\Models\Concert\ConcertVenue;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AdminConcertEdit extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('date')->required(),
                        Select::make('concert_venue_id')->options(ConcertVenue::orderBy('name')->get()->pluck('name', 'id')),
                        Select::make('concert_festival_id')->options(ConcertFestival::orderBy('name')->get()->pluck('name', 'id')),
                        Toggle::make('festival')->label('Festival')->default(false),
                        Toggle::make('support')->label('Support as main act')->default(false),

                    ]),
                Repeater::make('concertItems')
                    ->label('Artists')
                    ->relationship()
                    ->collapsible()
                    // ->collapsed()
                    ->reorderable()
                    ->columnSpan(2)
                    ->orderColumn('order')
                    ->schema([
                        Section::make()
                            ->schema([
                                Select::make('concert_artist_id')->options(ConcertArtist::orderBy('name')->get()->pluck('name', 'id')),
                                TextInput::make('setlistfm_url')->url(),
                                Toggle::make('support')->default(false),
                            ]),
                        Section::make()
                            ->schema([
                                FileUpload::make('image_url')
                                    ->directory('uploaded')
                                    ->visibility('private')
                                    ->getUploadedFileNameForStorageUsing(
                                        fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                                            ->prepend(date('YmdHis-')),
                                    ),
                            ]),

                    ]),
            ])->statePath('data');
    }

    public function create(): void
    {
        dd($this->form->getState());
    }

    public function render(): View
    {
        return view(
            'livewire.admin.concerts.admin-concert-edit'
        );
    }
}
