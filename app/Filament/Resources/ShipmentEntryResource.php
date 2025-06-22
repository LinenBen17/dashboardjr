<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Shipping;
use App\Filament\Resources\ShipmentEntryResource\Pages;
use App\Filament\Resources\ShipmentEntryResource\RelationManagers;
use App\Models\ShipmentEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShipmentEntryResource extends Resource
{
    protected static ?string $model = ShipmentEntry::class;

    protected static ?string $cluster = Shipping::class;

    protected static ?string $navigationLabel = 'Digitación de envíos';
    protected static ?string $modelLabel = 'Digitación';

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';
    protected static ?string $label = 'Digitación';

    //Sort in the cluster
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('mother')
                    ->required(),
                /* Forms\Components\TextInput::make('mother_guide_id')
                    ->maxLength(191), */
                Forms\Components\TextInput::make('sender_code')
                    ->numeric(),
                Forms\Components\TextInput::make('sender_name')
                    ->required(),
                Forms\Components\TextInput::make('sender_address')
                    ->required(),
                Forms\Components\TextInput::make('sender_phone')
                    ->tel()
                    ->required(),
                Forms\Components\TextInput::make('receiver_code')
                    ->required(),
                Forms\Components\TextInput::make('receiver_name')
                    ->required(),
                Forms\Components\TextInput::make('receiver_address')
                    ->required(),
                Forms\Components\TextInput::make('receiver_phone')
                    ->tel()
                    ->required(),
                Forms\Components\TextInput::make('prefix_origin')
                    ->required(),
                Forms\Components\TextInput::make('prefix_destination')
                    ->required(),
                Forms\Components\TextInput::make('town_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('product_description')
                    ->required(),
                Forms\Components\TextInput::make('pieces')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('unit_price')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('sender_total')
                    ->numeric(),
                Forms\Components\TextInput::make('receiver_total')
                    ->numeric(),
                Forms\Components\TextInput::make('total')
                    ->numeric(),
                Forms\Components\DateTimePicker::make('date_guide')
                    ->required(),
                Forms\Components\TextInput::make('payment_method_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('no_manifest')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('mother')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mother_guide_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sender_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sender_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sender_address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sender_phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('receiver_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('receiver_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('receiver_address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('receiver_phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('prefix_origin')
                    ->searchable(),
                Tables\Columns\TextColumn::make('prefix_destination')
                    ->searchable(),
                Tables\Columns\TextColumn::make('town_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pieces')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sender_total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('receiver_total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_guide')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_manifest')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListShipmentEntries::route('/'),
            'create' => Pages\CreateShipmentEntry::route('/create'),
            'edit' => Pages\EditShipmentEntry::route('/{record}/edit'),
        ];
    }
}
