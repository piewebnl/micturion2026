<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConcertFestivalResource\Pages;
use App\Models\Concert\ConcertFestival;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ConcertFestivalResource extends Resource
{
    protected static ?string $model = ConcertFestival::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                FileUpload::make('image_url')
                    ->directory('uploaded')
                    ->visibility('private')
                    ->getUploadedFileNameForStorageUsing(
                        fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                            ->prepend(date('YmdHis-')),
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([100])
            ->defaultPaginationPageOption(100)
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                ImageColumn::make('image_url')->label('Image'),
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
            'index' => Pages\ListConcertFestivals::route('/'),
            'create' => Pages\CreateConcertFestival::route('/create'),
            'edit' => Pages\EditConcertFestival::route('/{record}/edit'),
        ];
    }
}
