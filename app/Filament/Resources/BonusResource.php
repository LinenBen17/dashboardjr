<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\HumanResources;
use App\Filament\Resources\BonusResource\Pages;
use App\Filament\Resources\BonusResource\RelationManagers;
use App\Models\Bonus;
use App\Models\Vacation;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class BonusResource extends Resource
{
    protected static ?string $model = Bonus::class;

    protected static ?string $cluster = HumanResources::class;

    protected static ?string $navigationGroup = 'Planilla';

    protected static ?string $navigationLabel = 'Bonos';
    protected static ?string $modelLabel = 'Bono';

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    //Sort in the cluster
    protected static ?int $navigationSort = 13;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employee_payroll_id')
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
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->prefix('Q')
                    ->default(0)
                    ->numeric(),
                Forms\Components\Textarea::make('comments')
                    ->required()
                    ->maxLength(191),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_id')
                    ->numeric()
                    ->label('Empleado')
                    ->getStateUsing(function (Bonus $record) {
                        return DB::table('employee_payrolls')
                            ->join('employees', 'employee_payrolls.employee_id', '=', 'employees.id')
                            ->join('payrolls', 'employee_payrolls.payroll_id', '=', 'payrolls.id')
                            ->where('employee_payrolls.id', $record->employee_payroll_id)
                            ->select(DB::raw("CONCAT(employees.name, ' ', employees.last_name, ' - ', payrolls.name) as full_name_payroll"))
                            ->value('full_name_payroll');
                    })
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
            'index' => Pages\ManageBonuses::route('/'),
        ];
    }
}
