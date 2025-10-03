<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Shipping;
use App\Filament\Resources\ShipmentDeliveryResource\Pages;
use App\Filament\Resources\ShipmentDeliveryResource\RelationManagers;
use App\Models\ShipmentDelivery;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShipmentDeliveryResource extends Resource
{
    protected static ?string $model = ShipmentDelivery::class;

    protected static ?string $cluster = Shipping::class;

    protected static ?string $navigationLabel = 'Entregas de Envíos';
    protected static ?string $modelLabel = 'Entrega de Envío';

    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?string $label = 'Entrega de Envío';

    //Sort in the cluster
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('shipment_entry_id')
                    ->required()
                    ->maxLength(255),
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
            'index' => Pages\ShipmentDelivery::route('/'),
            // 'index' => Pages\ListShipmentDeliveries::route('/'),
            // 'create' => Pages\CreateShipmentDelivery::route('/create'),
            // 'edit' => Pages\EditShipmentDelivery::route('/{record}/edit'),
        ];
    }
}
