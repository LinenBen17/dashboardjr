<?php

namespace App\Filament\Resources\OrdencompraResource\Pages;

use App\Filament\Resources\OrdencompraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrdencompras extends ListRecords
{
    protected static string $resource = OrdencompraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
