<?php

namespace App\Filament\Resources\WarehouseOutgoResource\Pages;

use App\Filament\Resources\WarehouseOutgoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWarehouseOutgo extends EditRecord
{
    protected static string $resource = WarehouseOutgoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
