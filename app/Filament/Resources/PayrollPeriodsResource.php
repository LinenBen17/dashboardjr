<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollPeriodsResource\Pages;
use App\Filament\Resources\PayrollPeriodsResource\RelationManagers;
use App\Models\PayrollPeriods;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PayrollPeriodsResource extends Resource
{
    protected static ?string $model = PayrollPeriods::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('payroll_id')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('period_start')
                    ->required(),
                Forms\Components\DatePicker::make('period_end')
                    ->required(),
                Forms\Components\TextInput::make('period_number')
                    ->numeric(),
                Forms\Components\TextInput::make('year')
                    ->required(),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\DateTimePicker::make('closed_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payroll.name')
                    ->label('Tipo de Planilla')
                    ->sortable(),
                Tables\Columns\TextColumn::make('period_start')
                    ->label('Inicio del Período')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('period_end')
                    ->label('Fin del Período')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('period_number')
                    ->label('Tipo de Periodo')
                    ->getStateUsing(function (PayrollPeriods $record) {
                        if ($record->period_number === 1) {
                            return 'Quincena';
                        } elseif ($record->period_number === 2) {
                            return 'Fin de Mes';
                        } else {
                            return 'Número de período desconocido';
                        }
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('year')
                    ->label('Año')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                /* Tables\Columns\TextColumn::make('closed_at')
                    ->dateTime()
                    ->sortable(), */
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
                // Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('printPayroll')
                    ->label('Planilla')
                    ->icon('heroicon-o-printer')
                    ->url(fn(PayrollPeriods $record) => route('payroll.printPayroll', ['id' => $record->id]))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('printPayslips')
                    ->label('Boletas de Pago')
                    ->icon('heroicon-o-document-text')
                    ->color('warning')
                    ->url(function (PayrollPeriods $record) {
                        return route('payroll.generatePayslipsReport', [
                            'payroll_id' => $record->payroll_id,
                            'month' => Carbon::parse($record->period_start)->format('m'),
                            'year' => Carbon::parse($record->period_start)->format('Y'),
                            'period_number' => $record->period_number,
                        ]);
                    })
                    ->openUrlInNewTab(),
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
            'index' => Pages\ManagePayrollPeriods::route('/'),
        ];
    }
}
