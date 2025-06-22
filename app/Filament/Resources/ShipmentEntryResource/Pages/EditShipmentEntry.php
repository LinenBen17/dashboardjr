<?php

namespace App\Filament\Resources\ShipmentEntryResource\Pages;

use App\Filament\Resources\ShipmentEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShipmentEntry extends EditRecord
{
    protected static string $resource = ShipmentEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
