<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\HumanResources;
use App\Filament\Resources\DetailPayrollResource\Pages;
use App\Filament\Resources\DetailPayrollResource\RelationManagers;
use App\Models\Departament;
use App\Models\DetailPayroll;
use App\Models\District;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\DB;

class DetailPayrollResource extends Resource
{
    protected static ?string $model = DetailPayroll::class;

    protected static ?string $cluster = HumanResources::class;

    protected static ?string $navigationGroup = 'Planilla';

    protected static ?string $navigationLabel = 'Detalle de Planilla';
    protected static ?string $modelLabel = 'Detalle';

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    //Sort in the cluster
    protected static ?int $navigationSort = 12;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employee_id')
                    ->required()
                    ->relationship('employees', 'name', fn(Builder $query) => $query->select('id', DB::raw("CONCAT(name, ' ', last_name) as name")))
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        if ($state) {
                            $employee = Employee::find($state);
                            $departament = Departament::where('id', $employee->departament_id)->value('name');

                            Logger($employee);
                            Logger($departament);

                            $salary = District::where('year', now()->year)
                                ->where('name', ($departament == "Guatemala") ? 'CE1' : 'CE2')
                                ->value('salary');
                            $district = District::where('year', now()->year)
                                ->where('name', ($departament == "Guatemala") ? 'CE1' : 'CE2')
                                ->first();

                            $set('regular_salaries', $salary);
                            $set('district_id', $district->id);
                        }
                    }),
                Forms\Components\TextInput::make('regular_salaries')
                    ->required()
                    ->prefix('Q')
                    ->numeric(),
                Forms\Components\TextInput::make('bonus_of_law')
                    ->required()
                    ->prefix('Q')
                    ->numeric()
                    ->default(250.00),
                Forms\Components\TextInput::make('incentive_bonus')
                    ->required()
                    ->prefix('Q')
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('percentage_igss')
                    ->required()
                    ->numeric()
                    ->prefix('%')
                    ->default(4.83),
                Forms\Components\TextInput::make('percentage_isr')
                    ->required()
                    ->numeric()
                    ->prefix('%')
                    ->default(0),
                Forms\Components\TextInput::make('phone_discount')
                    ->tel()
                    ->required()
                    ->prefix('Q')
                    ->numeric()
                    ->default(0.00),
                Forms\Components\Select::make('district_id')
                    ->required()
                    ->relationship('districts', 'name')
                    ->disabled()
                    ->dehydrated(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_id')
                    ->label('Empleado')
                    ->getStateUsing(function (DetailPayroll $record) {
                        return $record->employees->name . ' ' . $record->employees->last_name;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('districts.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('regular_salaries')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bonus_of_law')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('incentive_bonus')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('percentage_igss')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('percentage_isr')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone_discount')
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
            'index' => Pages\ManageDetailPayrolls::route('/'),
        ];
    }
}
