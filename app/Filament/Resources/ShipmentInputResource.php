<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Shipping;
use App\Filament\Resources\ShipmentInputResource\Pages;
use App\Filament\Resources\ShipmentInputResource\RelationManagers;
use App\Models\ShipmentEntry;
use App\Models\ShipmentInput;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShipmentInputResource extends Resource
{
    protected static ?string $model = ShipmentEntry::class;

    protected static ?string $cluster = Shipping::class;

    protected static ?string $navigationLabel = 'Digitación de Envíos';
    protected static ?string $modelLabel = 'Digitación de Envío';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $label = 'Digitación de Envío';

    //Sort in the cluster
    protected static ?int $navigationSort = 50;

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
            'index' => Pages\ShipmentInput::route('/'),
            /* 'index' => Pages\ListShipmentInputs::route('/'),
            'create' => Pages\CreateShipmentInput::route('/create'),
            'edit' => Pages\EditShipmentInput::route('/{record}/edit'), */
        ];
    }
}
