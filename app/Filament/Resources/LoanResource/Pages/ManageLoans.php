<?php

namespace App\Filament\Resources\LoanResource\Pages;

use App\Filament\Resources\DiscountResource;
use App\Filament\Resources\LoanResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRecords;

class ManageLoans extends ManageRecords
{
    protected static string $resource = LoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),

            Action::make('Descuentos')
                ->label('Regresar a Descuentos')
                ->color('info')
                ->url(DiscountResource::getUrl('index'))
                ->icon('heroicon-o-arrow-left'),
        ];
    }
}
