<?php

namespace App\Filament\Resources\StatusEmployeeResource\Pages;

use App\Filament\Resources\StatusEmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageStatusEmployees extends ManageRecords
{
    protected static string $resource = StatusEmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
