<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Warehouse;
use App\Filament\Resources\WarehousesResource\Pages;
use App\Filament\Resources\WarehousesResource\RelationManagers;
use App\Models\Warehouses;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WarehousesResource extends Resource
{
    protected static ?string $model = Warehouses::class;

    protected static ?string $cluster = Warehouse::class;

    protected static ?string $navigationLabel = 'Bodegas';
    protected static ?string $modelLabel = 'Bodegas';

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    //Sort in the cluster
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(191),
                Forms\Components\Select::make('departament_id')
                    ->required()
                    ->relationship('departament', 'name'),
                Forms\Components\TextInput::make('prefix')
                    ->required()
                    ->maxLength(191),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('departament.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('prefix')
                    ->searchable(),
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
            'index' => Pages\ManageWarehouses::route('/'),
        ];
    }
}
