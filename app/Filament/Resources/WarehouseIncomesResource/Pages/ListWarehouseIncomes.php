<?php

namespace App\Filament\Resources\WarehouseIncomesResource\Pages;

use App\Filament\Resources\WarehouseIncomesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWarehouseIncomes extends ListRecords
{
    protected static string $resource = WarehouseIncomesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
