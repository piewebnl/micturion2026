<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConcertArtistResource\Pages;
use App\Models\Concert\ConcertArtist;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ConcertArtistResource extends Resource
{
    protected static ?string $model = ConcertArtist::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([100])
            ->defaultPaginationPageOption(100)
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
            ])
            ->defaultSort('name')
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
            'index' => Pages\ListConcertArtists::route('/'),
            'create' => Pages\CreateConcertArtist::route('/create'),
            'edit' => Pages\EditConcertArtist::route('/{record}/edit'),
        ];
    }
}
