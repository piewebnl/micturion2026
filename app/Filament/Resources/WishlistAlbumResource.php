<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WishlistAlbumResource\Pages;
use App\Models\Music\Album;
use App\Models\Wishlist\WishlistAlbum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class WishlistAlbumResource extends Resource
{
    protected static ?string $model = WishlistAlbum::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('persistent_album_id')
                    ->relationship(
                        name: 'album',
                        modifyQueryUsing: function (Builder $query) {
                            $query->with(['artist' => function ($q) {
                                $q->orderBy('sort_name', 'asc');
                            }])
                                ->join('artists', 'albums.artist_id', '=', 'artists.id')  // Adjust table and column names as needed
                                ->select('albums.*');  // Ensure you select the album columns after the join
                        }
                    )
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->artist->name} - {$record->name}  ")
                    ->searchable(['albums.name', 'artists.name']),
                Select::make('format')->options(['CD' => 'CD', 'LP' => 'LP']),
                Textarea::make('notes')->rows(5)->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([100])
            ->defaultPaginationPageOption(100)
            ->columns([
                ViewColumn::make('album.albumImage.slug')->view('filament.tables.columns.wishlist-album-image')->label(''),
                TextColumn::make('album.artist.name')->searchable()->sortable(),
                TextColumn::make('album.name')->searchable()->sortable(),
                TextColumn::make('format')->searchable()->sortable(),
                TextColumn::make('notes')->searchable(),
            ])
            ->defaultSort('album.artist.name')
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWishlistAlbums::route('/'),
            'create' => Pages\CreateWishlistAlbum::route('/create'),
            'edit' => Pages\EditWishlistAlbum::route('/{record}/edit'),
        ];
    }
}
