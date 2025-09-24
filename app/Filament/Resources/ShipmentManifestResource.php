<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Shipping;
use App\Filament\Resources\ShipmentManifestResource\Pages;
use App\Filament\Resources\ShipmentManifestResource\RelationManagers;
use App\Models\ShipmentManifest;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShipmentManifestResource extends Resource
{
    protected static ?string $model = ShipmentManifest::class;

    protected static ?string $cluster = Shipping::class;

    protected static ?string $navigationLabel = 'Manifiestos Auditables';
    protected static ?string $modelLabel = 'Manifiesto Auditable';

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $label = 'Manifiesto Auditable';

    //Sort in the cluster
    protected static ?int $navigationSort = 4;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
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
            'index' => Pages\ShipmentManifest::route('/'),
            // 'index' => Pages\ListShipmentManifests::route('/'),
            // 'create' => Pages\CreateShipmentManifest::route('/create'),
            // 'edit' => Pages\EditShipmentManifest::route('/{record}/edit'),
        ];
    }
}
