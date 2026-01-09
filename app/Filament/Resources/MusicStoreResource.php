<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MusicStoreResource\Pages;
use App\Filament\Resources\MusicStoreResource\Pages\ListMusicStores;
use App\Models\Wishlist\MusicStore;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use BackedEnum;

class MusicStoreResource extends Resource
{
    protected static ?string $model = MusicStore::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('key')->required(),
                TextInput::make('url')->url(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([100])
            ->defaultPaginationPageOption(100)
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('url')->sortable()->searchable(),
                TextColumn::make('key')->sortable()->searchable(),
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
            'index' => ListMusicStores::route('/'),
            // 'create' => Pages\CreateMusicStore::route('/create'),
            // 'edit' => Pages\EditMusicStore::route('/{record}/edit'),
        ];
    }
}
