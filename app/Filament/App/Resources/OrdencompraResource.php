<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\OrdencompraResource\Pages;
use App\Filament\App\Resources\OrdencompraResource\RelationManagers;
use App\Models\Ordencompra;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdencompraResource extends Resource
{
    protected static ?string $model = Ordencompra::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
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
            'index' => Pages\ListOrdencompras::route('/'),
            'create' => Pages\CreateOrdencompra::route('/create'),
            'edit' => Pages\EditOrdencompra::route('/{record}/edit'),
        ];
    }
}
