<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\HumanResources;
use App\Filament\Resources\DiscountResource\Pages;
use App\Filament\Resources\DiscountResource\RelationManagers;
use App\Models\Discount;
use DefStudio\SearchableInput\Forms\Components\SearchableInput;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class DiscountResource extends Resource
{
    protected static ?string $model = Discount::class;

    protected static ?string $cluster = HumanResources::class;

    protected static ?string $navigationGroup = 'Planilla';

    protected static ?string $navigationLabel = 'Descuentos';
    protected static ?string $modelLabel = 'Descuento';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-down';

    //Sort in the cluster
    protected static ?int $navigationSort = 14;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                /* Forms\Components\Select::make('employee_payroll_id')
                    ->label('Empleado')
                    ->searchable()
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
                    ->required(), */
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
                Forms\Components\Select::make('type')
                    ->options([
                        'anticipo' => 'Anticipo',
                        'ausencia' => 'Ausencia',
                        'otro' => 'Otros',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->default(now())
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->prefix('Q')
                    ->default(0)
                    ->numeric(),
                Forms\Components\TextInput::make('comments')
                    ->required()
                    ->maxLength(191),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_payroll_id')
                    ->numeric()
                    ->label('Empleado')
                    ->getStateUsing(function (Discount $record) {

                        return DB::table('employee_payrolls')
                            ->join('employees', 'employee_payrolls.employee_id', '=', 'employees.id')
                            ->join('payrolls', 'employee_payrolls.payroll_id', '=', 'payrolls.id')
                            ->where('employee_payrolls.id', $record->employee_payroll_id)
                            ->select(DB::raw("CONCAT(employees.name, ' ', employees.last_name, ' - ', payrolls.name) as full_name_payroll"))
                            ->value('full_name_payroll');
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('comments')
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
            'index' => Pages\ManageDiscounts::route('/'),
        ];
    }
}
