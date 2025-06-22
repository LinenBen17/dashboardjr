<?php

namespace App\Filament\Resources\ShipmentEntryResource\Pages;

use App\Filament\Resources\ShipmentEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShipmentEntries extends ListRecords
{
    protected static string $resource = ShipmentEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
