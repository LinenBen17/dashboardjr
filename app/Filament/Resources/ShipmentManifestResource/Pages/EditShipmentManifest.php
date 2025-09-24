<?php

namespace App\Filament\Resources\ShipmentManifestResource\Pages;

use App\Filament\Resources\ShipmentManifestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShipmentManifest extends EditRecord
{
    protected static string $resource = ShipmentManifestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
