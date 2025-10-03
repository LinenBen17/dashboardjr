<?php

namespace App\Filament\Resources\ShipmentDeliveryResource\Pages;

use App\Filament\Resources\ShipmentDeliveryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShipmentDeliveries extends ListRecords
{
    protected static string $resource = ShipmentDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
