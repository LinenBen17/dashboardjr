<?php

namespace App\Filament\Resources\WarehouseOutgoResource\Pages;

use App\Filament\Resources\WarehouseOutgoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWarehouseOutgos extends ListRecords
{
    protected static string $resource = WarehouseOutgoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
