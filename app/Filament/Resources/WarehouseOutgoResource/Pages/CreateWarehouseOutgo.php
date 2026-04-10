<?php

namespace App\Filament\Resources\WarehouseOutgoResource\Pages;

use App\Filament\Resources\WarehouseOutgoResource;
use App\Models\WarehouseIncomeGuide;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Log\Logger;

class CreateWarehouseOutgo extends CreateRecord
{
    protected static string $resource = WarehouseOutgoResource::class;

    public array $scannedGuides = [];
    public array $motherGuides = [];
    public array $childGuides = [];

    public array $scannedGuidesTimes = [];
    public array $motherGuidesTimes = [];
    public array $childGuidesTimes = [];

    private function normalizeGuide($guide): int
    {
        return (int) preg_replace('/\D/', '', $guide);
    }

    public function verifyIncomeGuide(string $guide)
    {
        $guide = trim($guide);

        $incomeGuide = WarehouseIncomeGuide::where('guide_number',  $this->normalizeGuide($guide))
            ->first();

        if (!$incomeGuide) {
            Notification::make()
                ->title('Guía no encontrada')
                ->body('La guía ' . $guide . ' no ha tenido movimiento de Ingreso a Bodega.')
                ->danger()
                ->persistent()
                ->send();
        }
        return $incomeGuide;
    }

    public function addMotherGuide(string $guide)
    {
        $guide = trim($guide);
        $isIncomeGuide = $this->verifyIncomeGuide($guide);

        if ($isIncomeGuide) {
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
    }

    public function addChildGuide(string $guide)
    {
        $guide = trim($guide);
        $isIncomeGuide = $this->verifyIncomeGuide($guide);

        if ($isIncomeGuide) {
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
                'guide_number' =>  $this->normalizeGuide($guide),
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
                ->modalContent(fn() => view('filament.resources.warehouse_outgos.modal-guides', [
                    'motherGuides' => $this->motherGuides,
                    'childGuides' => $this->childGuides,
                ]))

        ];
    }
}
