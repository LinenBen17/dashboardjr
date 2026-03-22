<?php

namespace App\Filament\Resources\ShipmentInputResource\Pages;

use App\Filament\Resources\ShipmentInputResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShipmentInputs extends ListRecords
{
    protected static string $resource = ShipmentInputResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
