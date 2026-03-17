<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeePayrollsResource;
use App\Filament\Resources\EmployeeResource;
use App\Models\Employee;
use App\Models\EmployeeParyolls;
use App\Models\EmployeePayrolls;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('Asignar Empleado a Planilla')
                ->color('info')
                ->form([
                    Section::make('')
                        ->columns(2)
                        ->schema([
                            Select::make('employee_id')
                                ->label('Empleado')
                                ->searchable()
                                ->options(
                                    fn() => Employee::where('status_id', 1)
                                        ->pluck(DB::raw("CONCAT(name, ' ', last_name) as full_name"), 'id')
                                        ->toArray()
                                )
                                ->required(),
                            Select::make('payroll_id')
                                ->label('Planilla')
                                ->relationship('payrolls', 'name')
                                ->required(),
                            Checkbox::make('active')
                                ->label('Activo')
                                ->default(true)
                                ->columnSpan(2),
                        ]),
                ])
                ->extraModalFooterActions([
                    Action::make('Visualición')
                        ->url(EmployeePayrollsResource::getUrl('index'))
                        ->openUrlInNewTab(false)
                        ->extraAttributes([
                            'style' => 'background-color: #FF5733; color: #FFFFFF; border-color: #FF5733;'
                        ]),
                ])
                ->action(function (array $data) {
                    EmployeePayrolls::create($data);
                    Notification::make()
                        ->title('Empleado asignado a planilla correctamente')
                        ->success()
                        ->send();
                }),
        ];
    }
}
