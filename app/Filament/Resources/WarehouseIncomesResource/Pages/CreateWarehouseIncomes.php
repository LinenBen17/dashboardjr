<?php

namespace App\Filament\Resources\WarehouseIncomesResource\Pages;

use App\Filament\Resources\WarehouseIncomesResource;
use App\Models\WarehouseIncomeGuide;
use Dom\Text;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Log\Logger;

class CreateWarehouseIncomes extends CreateRecord
{
    protected static string $resource = WarehouseIncomesResource::class;

    public array $scannedGuides = [];
    public array $motherGuides = [];
    public array $childGuides = [];

    public array $scannedGuidesTimes = [];
    public array $motherGuidesTimes = [];
    public array $childGuidesTimes = [];

    public function addMotherGuide(string $guide, $reincome)
    {
        $guide = trim($guide);

        if ($guide && !in_array($guide, $this->motherGuides)) {
            $this->motherGuides[] = $guide;
            $this->motherGuidesTimes[$guide] = now()->toDateTimeString(); // Guarda la hora
        } elseif (in_array($guide, $this->motherGuides)) {
            Notification::make()
                ->title('Guía ya escaneada')
                ->body('La guía ' . $guide . ' ya ha sido escaneada.')
                ->warning()
                ->persistent()
                ->send();
        }
    }

    public function addChildGuide(string $guide)
    {
        $guide = trim($guide);

        if ($guide && !in_array($guide, $this->childGuides)) {
            $this->childGuides[] = $guide;
            $this->childGuidesTimes[$guide] = now()->toDateTimeString(); // Guarda la hora
        } elseif (in_array($guide, $this->childGuides)) {
            Notification::make()
                ->title('Guía ya escaneada')
                ->body('La guía ' . $guide . ' ya ha sido escaneada.')
                ->warning()
                ->persistent()
                ->send();
        }
    }

    public function removeMotherGuide($index)
    {
        $guide = $this->motherGuides[$index] ?? null;
        unset($this->motherGuides[$index]);
        $this->motherGuides = array_values($this->motherGuides);
        if ($guide) unset($this->motherGuidesTimes[$guide]);
    }

    public function removeChildGuide($index)
    {
        $guide = $this->childGuides[$index] ?? null;
        unset($this->childGuides[$index]);
        $this->childGuides = array_values($this->childGuides);
        if ($guide) unset($this->childGuidesTimes[$guide]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['total_guides'] = count($this->motherGuides);
        $data['total_pieces'] = count($this->motherGuides) + count($this->childGuides);
        return $data;
    }

    protected function afterCreate(): void
    {
        $this->scannedGuides = array_merge($this->motherGuides, $this->childGuides);
        $this->scannedGuidesTimes = array_merge($this->motherGuidesTimes, $this->childGuidesTimes);

        Logger($this->scannedGuidesTimes);

        foreach ($this->scannedGuides as $guide) {
            $this->record->guides()->create([
                'guide_number' => str_replace('GU0', '', $guide),
                'mother_guide' => in_array($guide, $this->motherGuides) ? $guide : null,
                'child_guide' => in_array($guide, $this->childGuides) ? $guide : null,
                'scanned_at' => $this->scannedGuidesTimes[$guide],
            ]);
            Logger($this->scannedGuidesTimes[$guide]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Visualizar')
                ->label('Visualizar Guías')
                ->color('info')
                ->modalHeading('Guías Escaneadas')
                ->modalSubmitAction(false)
                ->modalContent(fn() => view('filament.resources.warehouse_incomes.modal-guides', [
                    'motherGuides' => $this->motherGuides,
                    'childGuides' => $this->childGuides,
                ]))

        ];
    }

    /* protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),

            Actions\Action::make('addMotherGuide')
                ->label('Añadir Guía Madre')
                ->icon('heroicon-o-plus')
                ->form([
                    Section::make()
                        ->schema([
                            TextInput::make('guide')
                                ->label('Guía Madre')
                                ->required()
                                ->maxLength(20)
                                ->placeholder('GU0XXXXXX'),
                        ])
                ])
                ->action(fn(array $data) => $this->addMotherGuide($data['guide'])),
        ];
    } */

    /* protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->label('Crear Ingreso')
            ->color('primary');
    } */
}
