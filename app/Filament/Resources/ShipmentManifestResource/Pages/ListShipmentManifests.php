<?php

namespace App\Filament\Resources\ShipmentManifestResource\Pages;

use App\Filament\Resources\ShipmentManifestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShipmentManifests extends ListRecords
{
    protected static string $resource = ShipmentManifestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
