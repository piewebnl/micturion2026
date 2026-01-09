<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlbumPurchaseResource\Pages\CreateAlbumPurchase;
use App\Filament\Resources\AlbumPurchaseResource\Pages\EditAlbumPurchase;
use App\Filament\Resources\AlbumPurchaseResource\Pages\ListAlbumPurchase;
use App\Livewire\Forms\Music\AlbumPurchaseSearchFormData;
use App\Models\Music\AlbumPurchase;
use App\Models\Music\Format;
use App\Models\Wishlist\MusicStore;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use BackedEnum;

class AlbumPurchaseResource extends Resource
{
    protected static ?string $model = AlbumPurchase::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        $albumPurchaseSearchFormData = new AlbumPurchaseSearchFormData;
        $searchFormData = $albumPurchaseSearchFormData->generate();

        return $schema
            ->schema([
                Select::make('album_id')->searchable()->options(
                    collect($searchFormData['albums_filament'])
                        ->toArray()
                ),
                Select::make('music_store_id')->options(MusicStore::orderBy('name')->get()->pluck('name', 'id')),
                Select::make('format_id')->options(Format::orderBy('name')->get()->pluck('name', 'id')),

                Grid::make(3)
                    ->schema([
                        TextInput::make('year')->required(),
                        TextInput::make('month'),
                        TextInput::make('day'),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([100])
            ->defaultPaginationPageOption(100)
            ->columns([
                TextColumn::make('artist.name')->sortable()->searchable(),
                TextColumn::make('album.name')->sortable()->searchable(),
                TextColumn::make('format.name')->sortable()->searchable(),
                TextColumn::make('musicStore.name')->sortable()->searchable(),
                TextColumn::make('year')->sortable()->searchable(),
                TextColumn::make('month')->sortable()->searchable(),
                TextColumn::make('day')->sortable()->searchable(),
            ])
            // ->defaultSort('name')
            ->filters([
                //
            ])
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAlbumPurchase::route('/'),
            'create' => CreateAlbumPurchase::route('/create'),
            'edit' => EditAlbumPurchase::route('/{record}/edit'),
        ];
    }
}
