<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Shipping;
use App\Filament\Resources\ShipmentEntryResource\Pages;
use App\Filament\Resources\ShipmentEntryResource\RelationManagers;
use App\Models\Product;
use App\Models\ShipmentEntry;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\DB;

class ShipmentEntryResource extends Resource
{
    protected static ?string $model = ShipmentEntry::class;

    protected static ?string $cluster = Shipping::class;

    protected static ?string $navigationLabel = 'Ingreso de Envíos';
    protected static ?string $modelLabel = 'Ingreso de Envío';

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';
    protected static ?string $label = 'Ingreso de Envíos';

    //Sort in the cluster
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                View::make('filament.resources.shipment_entries.script'),
                Grid::make()
                    ->columns(5)
                    ->schema([
                        // LADO DERECHO: 2 secciones
                        Grid::make()
                            ->columns(1)
                            ->columnSpan(1)
                            ->schema([
                                Section::make('')
                                    ->columns(1)
                                    ->schema([
                                        Grid::make()
                                            ->columns(1)
                                            ->schema([
                                                TextInput::make('mother')
                                                    ->label('No. Guía')
                                                    ->id('guia_madre')
                                                    ->required()
                                                    ->default(Filament::auth()->user()->custom_fields['serial_number'])
                                                    ->readOnly()
                                                    ->unique(),
                                                DatePicker::make('date_guide')
                                                    ->label('Fecha')
                                                    ->default(now())
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->required(),
                                                Select::make('payment_method_id')
                                                    ->label('Forma de Pago')
                                                    ->id('forma_pago')
                                                    ->required()
                                                    ->default(1)
                                                    ->relationship(
                                                        name: 'paymentMethod',
                                                        titleAttribute: 'name',
                                                        modifyQueryUsing: fn(Builder $query) => $query->orderBy('id'),
                                                    ),
                                            ]),
                                    ]),
                                Section::make('')
                                    ->columns(1)
                                    ->schema([
                                        Grid::make()
                                            ->columns(1)
                                            ->schema([
                                                TextInput::make('sender_total')
                                                    ->label('Total Remitente')
                                                    ->required()
                                                    ->default(0)
                                                    // ->disabled()
                                                    ->dehydrated()
                                                    ->numeric(),
                                                TextInput::make('receiver_total')
                                                    ->label('Total Destinatario')
                                                    ->required()
                                                    ->default(0)
                                                    // ->disabled()
                                                    ->dehydrated()
                                                    ->numeric(),
                                                TextInput::make('total')
                                                    ->label('Monto Total')
                                                    ->required()
                                                    // ->disabled()
                                                    ->dehydrated()
                                                    ->numeric(),
                                            ]),
                                    ]),
                            ]),
                        // LADO IZQUIERDO: 3 secciones
                        Grid::make()
                            ->columns(1)
                            ->columnSpan(4)
                            ->schema([
                                Section::make('')
                                    ->columns(4)
                                    ->schema([
                                        Grid::make()
                                            ->columns(4)
                                            ->schema([
                                                TextInput::make('sender_code')
                                                    ->label('Código Remitente')
                                                    ->id('codigo_remitente')
                                                    ->numeric()
                                                    ->extraAlpineAttributes([
                                                        'x-ref' => 'codeSenderInput',
                                                        // cuando llegue el evento, enfoca el input
                                                        'x-on:focus-codeSender.window' => '$refs.codeSenderInput.focus()',
                                                    ]),
                                                TextInput::make('sender_name')
                                                    ->label('Nombre Remitente')
                                                    ->required(),
                                                TextInput::make('sender_address')
                                                    ->label('Dirección Remitente')
                                                    ->required(),
                                                TextInput::make('sender_phone')
                                                    ->label('Teléfono Remitente')
                                                    ->tel()
                                                    ->required(),
                                            ]),
                                        Grid::make()
                                            ->columns(4)
                                            ->schema([
                                                TextInput::make('receiver_code')
                                                    ->label('Código Destinatario')
                                                    ->id('codigo_destinatario')
                                                    ->numeric(),
                                                TextInput::make('receiver_name')
                                                    ->label('Nombre Destinatario')
                                                    ->required(),
                                                TextInput::make('receiver_address')
                                                    ->label('Dirección Destinatario')
                                                    ->required(),
                                                TextInput::make('receiver_phone')
                                                    ->label('Teléfono Destinatario')
                                                    ->tel()
                                                    ->required(),
                                            ]),
                                    ]),

                                Section::make('')
                                    ->columns(4)
                                    ->schema([
                                        Grid::make()
                                            ->columns(3)
                                            ->schema([
                                                TextInput::make('prefix_origin')
                                                    ->label('Origen')
                                                    ->required(),
                                                TextInput::make('prefix_destination')
                                                    ->label('Destino')
                                                    ->required(),
                                                Select::make('town_id')
                                                    ->label('Municipio')
                                                    ->relationship('towns', 'name')
                                                    ->required(),
                                            ]),
                                    ]),

                                Section::make('')
                                    ->columns(1)
                                    ->schema([
                                        Repeater::make('products')
                                            ->label('Productos')
                                            ->reorderable(false)
                                            ->columns(5)
                                            ->minItems(1)
                                            ->reactive()
                                            ->afterStateUpdated(
                                                function (Get $get, Set $set) {
                                                    self::recalcTotals($get, $set);
                                                }
                                            )
                                            ->deleteAction(
                                                function (Get $get, Set $set, Action $action) {
                                                    self::recalcTotals($get, $set);
                                                    $action->hidden(            // oculta el botón…
                                                        fn(array $arguments, Repeater $component): bool =>
                                                        // …cuando el ítem actual es el primero del arreglo
                                                        $arguments['item'] === array_key_first($component->getState())
                                                    );
                                                }
                                            )
                                            ->schema([
                                                // Consultar si es mejor un select o Input Text
                                                TextInput::make('product_id')
                                                    ->label('Código')
                                                    ->required()
                                                    ->reactive()
                                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                                        $productDescription = DB::table('products')
                                                            ->where('id', $state)
                                                            ->value('name');

                                                        if ($productDescription) {
                                                            $set('product_description', $productDescription);
                                                        } else {
                                                            $set('product_description', '0');
                                                        }
                                                    }),
                                                TextInput::make('pieces')
                                                    ->label('Piezas')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('product_description')
                                                    ->label('Descripción')
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->required(),
                                                TextInput::make('unit_price')
                                                    ->label('Precio Unitario')
                                                    ->numeric()
                                                    ->required()
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                                        $pieces = $get('pieces') ?: 0;
                                                        $set('subtotal', $state * $pieces);
                                                    }),

                                                TextInput::make('subtotal')
                                                    ->label('Subtotal')
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->numeric(),
                                            ]),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    protected static function recalcTotals(Get $get, Set $set): void
    {
        // Suma los subtotales válidos
        $senderTotal = collect($get('products'))
            ->pluck('subtotal')
            ->filter()          // ignora nulos / vacíos
            ->sum();

        $paymentMethodID = intval(trim($get('payment_method_id')));

        $paymentMethod = DB::table('payment_methods')
            ->where('id', $paymentMethodID)
            ->value('name');

        if ($paymentMethod == 'Contado') {
            $set('receiver_total', $senderTotal);
            $set('sender_total',  0);
        } else {
            $set('sender_total', $senderTotal);
            $set('receiver_total', 0);
        }

        // total = sender_total + receiver_total
        $set('total', ($get('sender_total') ?? 0) + ($get('receiver_total') ?? 0));
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
