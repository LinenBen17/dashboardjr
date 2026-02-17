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
                Tables\Columns\TextColumn::make('numero_orden')
                ->label('N° Orden')
                ->numeric()
                ->sortable(),

            Tables\Columns\TextColumn::make('fecha')
                ->date()
                ->sortable(),

            Tables\Columns\TextColumn::make('proveedor')
                ->searchable(),

            Tables\Columns\TextColumn::make('items_count')
                ->label('Ítems')
                ->getStateUsing(fn ($record) => count($record->items ?? [])),

            Tables\Columns\TextColumn::make('total_general')
                ->label('Total')
                ->money('GTQ') // Quetzales
                ->sortable(),

            Tables\Columns\TextColumn::make('created_at')
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
            'index' => Pages\ListOrdencompras::route('/'),
            'create' => Pages\CreateOrdencompra::route('/create'),
            'edit' => Pages\EditOrdencompra::route('/{record}/edit'),
        ];
    }
}
