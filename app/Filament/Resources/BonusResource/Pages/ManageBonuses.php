<?php

namespace App\Filament\Resources\BonusResource\Pages;

use App\Filament\Resources\BonusResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRecords;

class ManageBonuses extends ManageRecords
{
    protected static string $resource = BonusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('resetStars')
                ->icon('heroicon-m-x-mark')
                ->color('danger')
                ->requiresConfirmation(),
            Actions\CreateAction::make(),
        ];
    }
}
