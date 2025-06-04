<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WarehouseOutgoResource\Pages;
use App\Filament\Resources\WarehouseOutgoResource\RelationManagers;
use App\Models\WarehouseOutgo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WarehouseOutgoResource extends Resource
{
    protected static ?string $model = WarehouseOutgo::class;

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
            'index' => Pages\ListWarehouseOutgos::route('/'),
            'create' => Pages\CreateWarehouseOutgo::route('/create'),
            'edit' => Pages\EditWarehouseOutgo::route('/{record}/edit'),
        ];
    }
}
