<?php

namespace App\Filament\Resources\WarehouseOutgoResource\Pages;

use App\Filament\Resources\WarehouseOutgoResource;
use App\Models\WarehouseIncomeGuide;
use App\Models\WarehouseOutgoGuide;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditWarehouseOutgo extends EditRecord
{
    protected static string $resource = WarehouseOutgoResource::class;

    public array $scannedGuides = [];
    public array $motherGuides = [];
    public array $childGuides = [];

    public array $scannedGuidesTimes = [];
    public array $motherGuidesTimes = [];
    public array $childGuidesTimes = [];

    //function to start
    public function mount($record): void
    {
        parent::mount($record);
        $this->getGuides();
    }

    public function verifyIncomeGuide(string $guide)
    {
        $guide = trim($guide);

        $incomeGuide = WarehouseIncomeGuide::where('guide_number', str_replace('GU0', '', $guide))
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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['total_guides'] = count($this->motherGuides);
        $data['total_pieces'] = count($this->motherGuides) + count($this->childGuides);
        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->guides()->delete();
        $this->scannedGuides = array_merge($this->motherGuides, $this->childGuides);

        foreach ($this->scannedGuides as $guide) {
            $this->record->guides()->create([
                'guide_number' => str_replace('GU0', '', $guide),
                'mother_guide' => in_array($guide, $this->motherGuides) ? $guide : null,
                'child_guide' => in_array($guide, $this->childGuides) ? $guide : null,
                'scanned_at' => array_key_exists($guide, $this->motherGuidesTimes) ? $this->motherGuidesTimes[$guide] : $this->childGuidesTimes[$guide],
            ]);
        }
    }

    public function getGuides()
    {
        $this->motherGuides = WarehouseOutgoGuide::where('warehouse_outgo_id', $this->record->id)
            ->whereNotNull('mother_guide')
            ->pluck('mother_guide')
            ->toArray();
        $this->motherGuidesTimes = WarehouseOutgoGuide::where('warehouse_outgo_id', $this->record->id)
            ->whereNotNull('mother_guide')
            ->pluck('scanned_at', 'mother_guide')
            ->toArray();
        $this->childGuides = WarehouseOutgoGuide::where('warehouse_outgo_id', $this->record->id)
            ->whereNotNull('child_guide')
            ->pluck('child_guide')
            ->toArray();
        $this->childGuidesTimes = WarehouseOutgoGuide::where('warehouse_outgo_id', $this->record->id)
            ->whereNotNull('child_guide')
            ->pluck('scanned_at', 'child_guide')
            ->toArray();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Action::make('Visualizar')
                ->label('Visualizar Guías')
                ->color('info')
                ->modalHeading('Guías Escaneadas')
                ->modalSubmitAction(false)
                ->modalContent(fn() => view('filament.resources.warehouse_outgos.modal-guides', [
                    'motherGuides' => $this->motherGuides,
                    'childGuides' => $this->childGuides,
                ])),
            Action::make('Imprimir')
                ->label('Imprimir')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->extraAttributes(['target' => '_blank'])
                ->url(route('manifest_outgo_format', $this->record->id)),
        ];
    }
}
