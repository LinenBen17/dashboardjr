<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\HumanResources;
use App\Filament\Resources\DetailPayrollResource\Pages;
use App\Filament\Resources\DetailPayrollResource\RelationManagers;
use App\Models\Departament;
use App\Models\DetailPayroll;
use App\Models\District;
use App\Models\Employee;
use App\Models\Payroll;
use DefStudio\SearchableInput\Forms\Components\SearchableInput;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\View;
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
                SearchableInput::make('employee_payroll_id')
                    ->label('Empleado')
                    ->options(function () {
                        return DB::table('employee_payrolls')
                            ->join('employees', 'employee_payrolls.employee_id', '=', 'employees.id')
                            ->join('payrolls', 'employee_payrolls.payroll_id', '=', 'payrolls.id')
                            ->select('employee_payrolls.id', DB::raw("CONCAT(employees.name, ' ', employees.last_name, ' - ', payrolls.name) as full_name_payroll"))
                            ->where('employee_payrolls.active', 1)
                            ->orderBy('employees.name')
                            ->pluck('full_name_payroll', 'employee_payrolls.id')
                            ->toArray();
                    })
                    ->required(),
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn($query) =>
                $query->with([
                    'employeePayroll.employee',
                    'employeePayroll.payroll',
                ])
            )
            ->columns([
                Tables\Columns\TextColumn::make('employeePayroll.employee')
                    ->label('Empleado')
                    ->getStateUsing(
                        fn($record) => ($record->employeePayroll?->employee?->name ?? '') . ' ' .
                            ($record->employeePayroll?->employee?->last_name ?? '')
                    )
                    ->searchable(query: function ($query, $search) {
                        $query->whereHas('employeePayroll.employee', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(query: function ($query, $direction) {
                        $query->whereHas('employeePayroll.employee', function ($q) use ($direction) {
                            $q->orderBy('name', $direction)
                                ->orderBy('last_name', $direction);
                        });
                    }),
                Tables\Columns\TextColumn::make('employeePayroll.payroll.name')
                    ->label('Planilla')
                    ->getStateUsing(
                        fn(DetailPayroll $record) =>
                        $record->employeePayroll?->payroll?->name ?? 'N/A'
                    )
                    ->searchable(query: function ($query, $search) {
                        $query->whereHas('employeePayroll.payroll', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(query: function ($query, $direction) {
                        $query->whereHas('employeePayroll.payroll', function ($q) use ($direction) {
                            $q->orderBy('name', $direction);
                        });
                    }),
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
