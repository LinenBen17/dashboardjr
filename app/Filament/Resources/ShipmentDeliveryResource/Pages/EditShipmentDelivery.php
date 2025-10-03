<?php

namespace App\Filament\Resources\ShipmentDeliveryResource\Pages;

use App\Filament\Resources\ShipmentDeliveryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShipmentDelivery extends EditRecord
{
    protected static string $resource = ShipmentDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
