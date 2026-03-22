<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerSpecialRatesResource\Pages;
use App\Filament\Resources\CustomerSpecialRatesResource\RelationManagers;
use App\Models\CustomerSpecialRates;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerSpecialRatesResource extends Resource
{
    protected static ?string $model = CustomerSpecialRates::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    //Ocultar del panel 
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('customer_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('product_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('special_price')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer_id')
                    ->label('Cliente')
                    ->numeric()
                    ->getStateUsing(function ($record) {
                        return $record->customer ? $record->customer->name : 'N/A';
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_id')
                    ->label('Producto')
                    ->numeric()
                    ->getStateUsing(function ($record) {
                        return $record->product ? $record->product->name : 'N/A';
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('special_price')
                    ->label('Tarifa Especial')
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCustomerSpecialRates::route('/'),
        ];
    }
}
