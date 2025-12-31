<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConcertResource\Pages;
use App\Models\Concert\Concert;
use App\Models\Concert\ConcertArtist;
use App\Models\Concert\ConcertFestival;
use App\Models\Concert\ConcertVenue;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ConcertResource extends Resource
{
    protected static ?string $model = Concert::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected function shouldPersistTableSortInSession(): bool
    {
        return true;
    }

    protected function afterCreate()
    {
        // Runs after the form fields are saved to the database.
    }

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('date')->required(),
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([100])
            ->defaultPaginationPageOption(100)
            ->columns([
                TextColumn::make('date')->date('d M Y')->sortable()->searchable(),
                TextColumn::make('concertArtists.concertArtist.name')->sortable()->searchable(),
                TextColumn::make('concertVenue.name')->sortable()->searchable(),

            ])
            ->defaultSort('date', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // ConcertItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConcerts::route('/'),
            'create' => Pages\CreateConcert::route('/create'),
            'edit' => Pages\EditConcert::route('/{record}/edit'),
        ];
    }
}
