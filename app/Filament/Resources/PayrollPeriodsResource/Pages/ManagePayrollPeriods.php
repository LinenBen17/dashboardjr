<?php

namespace App\Filament\Resources\PayrollPeriodsResource\Pages;

use App\Filament\Resources\DetailPayrollResource;
use App\Filament\Resources\EmployeeResource;
use App\Filament\Resources\PayrollPeriodsResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePayrollPeriods extends ManageRecords
{
    protected static string $resource = PayrollPeriodsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('DetallePlanilla')
                ->label('Regresar al Detalle de Planilla')
                ->color('info')
                ->url(DetailPayrollResource::getUrl('index'))
                ->icon('heroicon-o-arrow-left'),
        ];
    }
}
