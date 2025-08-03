<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrdencompraResource\Pages;
use App\Filament\Resources\OrdencompraResource\RelationManagers;
use App\Models\Ordencompra;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class OrdencompraResource extends Resource
{
    protected static ?string $model = Ordencompra::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            // Sección 1: Datos generales
            Forms\Components\Section::make('Ingreso de órdenes de compra')
                ->description('Llene los campos requeridos para registrar una nueva orden de compra.')
                ->schema([
                    Forms\Components\DatePicker::make('fecha')
                        ->label('Fecha')
                        ->required()
                        ->default(now()),

                    Forms\Components\TextInput::make('numero_orden')
                        ->label('Número de Orden')
                        ->required()
                        ->numeric()
                        ->maxLength(191),

                    Forms\Components\TextInput::make('ruta')
                        ->label('Ruta')
                        ->required()
                        ->maxLength(191),

                    Forms\Components\TextInput::make('placa')
                        ->label('Placa')
                        ->required()
                        ->maxLength(191),

                    Forms\Components\TextInput::make('proveedor')
                        ->label('Proveedor')
                        ->required()
                        ->maxLength(191),
                ])
                ->columns(4),

            // Sección 2: Ítems dinámicos
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->label('Ítems')
                        ->schema([
                            Forms\Components\TextInput::make('descripcion')
                                ->label('Descripción')
                                ->required()
                                ->maxLength(191)
                                ->columnSpan(2),

                            Forms\Components\TextInput::make('cantidad')
                                ->label('Cantidad')
                                ->numeric()
                                ->required()
                                ->default(1)
                                ->minValue(0.01)
                                ->reactive()
                                ->afterStateUpdated(fn ($state, Forms\Set $set, Forms\Get $get) => 
                                    $set('subtotal', round($state * $get('precio_unitario'), 2))
                                ),

                            Forms\Components\TextInput::make('precio_unitario')
                                ->label('Precio Unitario')
                                ->numeric()
                                ->required()
                                ->prefix('Q')
                                ->default(0)
                                ->reactive()
                                ->afterStateUpdated(fn ($state, Forms\Set $set, Forms\Get $get) => 
                                    $set('subtotal', round($get('cantidad') * $state, 2))
                                ),

                            Forms\Components\TextInput::make('subtotal')
                                ->label('Sub Total')
                                ->numeric()
                                ->prefix('Q')
                                ->readOnly()
                                ->reactive()
                                ->default(0)
                                ->extraInputAttributes(['class' => 'text-right font-bold text-lg']),
                        ])
                        ->columns(4)
                        ->defaultItems(1)
                        ->addable()
                        ->deletable()
                        ->collapsible()
                        ->cloneable()
                        ->orderColumn('sort_order')
                        ->itemLabel(fn (array $state): ?string => $state['descripcion'] ?? null)
                        ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                            // Recalcular total_general cada vez que cambie cualquier ítem
                            $items = $get('items') ?? [];
                            $total = collect($items)->sum('subtotal');
                            $set('total_general', round($total, 2));
                        }),
                ]),

            // Sección 3: Total general
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\TextInput::make('total_general')
                        ->label('Total General')
                        ->numeric()
                        ->prefix('Q')
                        ->readOnly()
                        ->reactive()
                        ->extraInputAttributes(['class' => 'font-bold text-lg'])
                        ->dehydrated()
                        ->default(0),

                    Forms\Components\Textarea::make('observaciones')
                        ->label('Observaciones')
                        ->maxLength(191)
                        ->columnSpanFull(),
                ]),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_orden')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ruta')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('proveedor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('placa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cantidad')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('precioUnitario')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sub-total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('observaciones')
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
