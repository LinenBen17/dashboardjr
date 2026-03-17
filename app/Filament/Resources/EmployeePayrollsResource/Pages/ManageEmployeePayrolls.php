<?php

namespace App\Filament\Resources\EmployeePayrollsResource\Pages;

use App\Filament\Resources\EmployeePayrollsResource;
use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRecords;

class ManageEmployeePayrolls extends ManageRecords
{
    protected static string $resource = EmployeePayrollsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Planillas')
                ->label('Regresar a Empleados')
                ->color('info')
                ->url(EmployeeResource::getUrl('index'))
                ->icon('heroicon-o-arrow-left'),
        ];
    }
}
