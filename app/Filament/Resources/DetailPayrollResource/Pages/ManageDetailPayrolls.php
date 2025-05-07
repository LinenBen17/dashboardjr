<?php

namespace App\Filament\Resources\DetailPayrollResource\Pages;

use App\Filament\Resources\DetailPayrollResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDetailPayrolls extends ManageRecords
{
    protected static string $resource = DetailPayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
