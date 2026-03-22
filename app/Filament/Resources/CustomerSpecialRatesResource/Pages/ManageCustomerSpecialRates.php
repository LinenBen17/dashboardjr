<?php

namespace App\Filament\Resources\CustomerSpecialRatesResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Filament\Resources\CustomerSpecialRatesResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCustomerSpecialRates extends ManageRecords
{
    protected static string $resource = CustomerSpecialRatesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            Actions\Action::make('clientes')
                ->label('Regresar a Clientes')
                ->color('info')
                ->icon('heroicon-o-arrow-left')
                ->url(CustomerResource::getUrl('index')),
        ];
    }
}
