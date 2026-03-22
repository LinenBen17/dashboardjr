<?php

namespace App\Filament\Resources\ShipmentInputResource\Pages;

use App\Filament\Resources\ShipmentInputResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShipmentInput extends EditRecord
{
    protected static string $resource = ShipmentInputResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
