<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Warehouse;
use App\Filament\Resources\WarehouseIncomesResource\Pages;
use App\Filament\Resources\WarehouseIncomesResource\RelationManagers;
use App\Livewire\WarehouseGuides;
use App\Models\Employee;
use App\Models\Route;
use App\Models\WarehouseIncomes;
use App\Models\Warehouses;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\DB;

class WarehouseIncomesResource extends Resource
{
    protected static ?string $model = WarehouseIncomes::class;

    protected static ?string $cluster = Warehouse::class;

    protected static ?string $navigationLabel = 'Ingreso Bodega';
    protected static ?string $modelLabel = 'Ingreso Bodega';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-left-end-on-rectangle';

    //Sort in the cluster
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                View::make('filament.resources.warehouse_incomes.script'),
                Section::make('')
                    ->columns(3)
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                /* Forms\Components\Checkbox::make('reincome')
                                    ->label('Re-ingreso')
                                    ->id('reincome'), */
                                Forms\Components\TextInput::make('manifest_code')
                                    ->label('Manifiesto')
                                    ->unique(ignoreRecord: true)
                                    ->disabled()
                                    ->dehydrated()
                                    ->required(),
                                Forms\Components\DatePicker::make('arrived_date')
                                    ->label('Fecha de Ingreso')
                                    ->default(now())
                                    ->required(),
                                Forms\Components\Select::make('warehouse_id')
                                    ->label('Bodega')
                                    ->required()
                                    ->id('warehouse_id')
                                    ->disabledOn('edit')
                                    ->live(onBlur: true)
                                    ->options(function () {
                                        $warehouses = DB::table('warehouses')
                                            ->where('departament_id', Filament::auth()->user()->custom_fields['departament_id']);

                                        if (Filament::auth()->user()->name == 'Super Admin') {
                                            $warehouses = DB::table('warehouses');
                                        }
                                        return $warehouses->pluck('name', 'id');
                                    })
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $lastCode = DB::table('warehouse_incomes')
                                            ->where('warehouse_id', $state)
                                            ->orderBy('created_at', 'desc')
                                            ->value('manifest_code');

                                        $prefix = DB::table('warehouses')
                                            ->where('id', $state)
                                            ->value('prefix');

                                        $newCode = $lastCode ? substr($lastCode, 4) + 1 : 1;
                                        $set('manifest_code', $prefix . '-' . $newCode);
                                    }),
                            ]),
                        Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('route_id')
                                    ->label('Ruta Que Ingresa')
                                    ->live(onBlur: true)
                                    ->options(function () {
                                        $routes =  Route::query()
                                            ->join('agencies', 'routes.agency_id', '=', 'agencies.id')
                                            ->join('departaments', 'agencies.departament_id', '=', 'departaments.id')
                                            ->where('departaments.id', Filament::auth()->user()->custom_fields['departament_id'])
                                            ->orderBy('routes.name')
                                            ->pluck('routes.name', 'routes.id');

                                        if (Filament::auth()->user()->name == 'Super Admin') {
                                            $routes =  Route::query()
                                                ->orderBy('routes.name')
                                                ->pluck('routes.name', 'routes.id');
                                        }

                                        return $routes;
                                    })
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $nameDriver = DB::table('routes')
                                            ->leftJoin('employees', 'routes.employee_id', '=', 'employees.id')
                                            ->select('employees.name', 'employees.last_name')
                                            ->where('routes.id', $state)
                                            ->first();

                                        $set('driver', $nameDriver->name . ' ' . $nameDriver->last_name);
                                    })
                                    ->required(),
                                Forms\Components\TextInput::make('driver')
                                    ->label('Piloto Que Ingresa')
                                    ->required()
                                    ->maxLength(191),
                                Forms\Components\Select::make('person_scans')
                                    ->label('Persona Que Escanea')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->options(function () {
                                        return [
                                            Filament::auth()->user()->id => Filament::auth()->user()->name,
                                        ];
                                    })
                                    ->default(Filament::auth()->user()->id),
                            ]),
                    ]),
                Section::make('')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('guideInput')
                                    ->label('Número de Guía')
                                    ->id('guideInput')
                                    ->reactive()
                                    ->statePath('guideInput'),
                                Forms\Components\TextInput::make('last_mother_guide')
                                    ->label('Última Guía Madre')
                                    ->id('last_mother_guide')
                                    ->disabled()
                                    ->dehydrated(),
                                Forms\Components\TextInput::make('last_child_guide')
                                    ->label('Última Guía Hija')
                                    ->id('last_child_guide')
                                    ->disabled()
                                    ->dehydrated(),
                            ]),
                        Grid::make(2)
                            ->schema([
                                Section::make('')
                                    ->columns(3)
                                    ->schema([
                                        Placeholder::make('total_piezas')
                                            ->content(fn($livewire) => count($livewire->motherGuides) + count($livewire->childGuides))
                                            ->label('Total Piezas')
                                            ->extraAttributes([
                                                'style' => 'font-size: 24pt;',
                                            ]),
                                        Placeholder::make('total_guias')
                                            ->content(fn($livewire) => count($livewire->motherGuides))
                                            ->label('Total Guías')
                                            ->extraAttributes([
                                                'style' => 'font-size: 24pt;',
                                            ]),
                                        Placeholder::make('total_hijas')
                                            ->content(fn($livewire) => count($livewire->childGuides))
                                            ->label('Total Hijas')
                                            ->extraAttributes([
                                                'style' => 'font-size: 24pt;',
                                            ]),
                                    ]),
                            ]),
                    ]),
                Section::make('')
                    ->columns(3)
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('codigo_remitente')
                                    ->label('Código Remitente')
                                    ->id('codigo_remitente')
                                    ->readOnly(true)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('remitente')
                                    ->label('Remitente')
                                    ->id('remitente')
                                    ->readOnly(true)
                                    ->columnSpan(2),
                            ]),
                        Grid::make(3)
                            ->schema([
                                Placeholder::make('empty')
                                    ->content('')
                                    ->label('')
                                    ->extraAttributes([
                                        'style' => 'font-size: 24pt;',
                                    ]),
                                Forms\Components\TextInput::make('dir_remitente')
                                    ->label('Dirección Remitente')
                                    ->id('dir_remitente')
                                    ->readOnly(true)
                                    ->columnSpan(2),
                            ]),
                        /* Placeholder::make('guías hijas')
                            ->content(fn($livewire) => implode(', ', $livewire->childGuides)),
                        Placeholder::make('guías madres')
                            ->content(fn($livewire) => implode(', ', str_replace('GU0', '', $livewire->motherGuides))), */
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('manifest_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('arrived_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('warehouses.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('routes.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('driver')
                    ->searchable(),
                Tables\Columns\TextColumn::make('person_scans')
                    ->getStateUsing(function (WarehouseIncomes $record) {
                        $name = Employee::find($record->person_scans);
                        return $name ? $name->name . ' ' . $name->last_name : '';
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_pieces')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_guides')
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
                Tables\Actions\Action::make('Imprimir')
                    ->label('Imprimir')
                    ->url(fn($record) => route('manifest_income_format', $record->id))
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListWarehouseIncomes::route('/'),
            'create' => Pages\CreateWarehouseIncomes::route('/create'),
            'edit' => Pages\EditWarehouseIncomes::route('/{record}/edit'),
        ];
    }
}
