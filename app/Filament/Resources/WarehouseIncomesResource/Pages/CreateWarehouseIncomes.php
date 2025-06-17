<?php

namespace App\Filament\Resources\WarehouseIncomesResource\Pages;

use App\Filament\Resources\WarehouseIncomesResource;
use App\Models\WarehouseIncomeGuide;
use App\Models\WarehouseOutgoGuide;
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

    public function validarGuiaParaIngreso(string $guide, int $currentWarehouseId): string
    {
        $ingresos = WarehouseIncomeGuide::where('guide_number', $guide)
            ->with('warehouseIncome')
            ->get();

        if ($ingresos->isEmpty()) {
            return 'OK'; // Nunca ha ingresado
        }

        foreach ($ingresos as $ingreso) {
            $bodegaIngreso = $ingreso->warehouseIncome->warehouse_id;

            $tieneSalida = WarehouseOutgoGuide::where('guide_number', $guide)
                ->whereHas('warehouseOutgo', function ($query) use ($bodegaIngreso) {
                    $query->where('origin_warehouse_id', $bodegaIngreso);
                })
                ->exists();

            if ($bodegaIngreso === $currentWarehouseId) {
                if (!$tieneSalida) {
                    return 'YA_INGRESO_NO_SALIO';
                } else {
                    return 'REINGRESO';
                }
            } else {
                if (!$tieneSalida) {
                    return 'INGRESO_OTRA_NO_SALIO';
                } else {
                    return 'OK'; // Ya salió de otra bodega, ingreso válido aquí
                }
            }
        }

        return 'ERROR';
    }

    public function addMotherGuide(string $guide, $currentWarehouseId)
    {
        $guide = trim($guide);

        if (!$guide) return;

        if (in_array($guide, $this->motherGuides)) {
            Notification::make()
                ->title('Guía ya escaneada')
                ->body('La guía ' . $guide . ' ya ha sido escaneada.')
                ->warning()
                ->persistent()
                ->send();
            return;
        }

        // Validar reingreso
        $estado = $this->validarGuiaParaIngreso(str_replace('GU0', '', $guide), $currentWarehouseId);
        Logger($estado);

        if ($estado === 'YA_INGRESO_NO_SALIO') {
            Notification::make()
                ->title('Ingreso no permitido')
                ->body("La guía $guide ya fue ingresada en esta bodega y no ha salido.")
                ->danger()
                ->persistent()
                ->send();
            return;
        }

        if ($estado === 'INGRESO_OTRA_NO_SALIO') {
            Notification::make()
                ->title('Ingreso no permitido')
                ->body("La guía $guide fue ingresada en otra bodega y no ha salido de allí.")
                ->danger()
                ->persistent()
                ->send();
            return;
        }

        // Si es 'OK' o 'REINGRESO', se permite agregar
        if ($estado === 'REINGRESO') {
            Notification::make()
                ->title('Reingreso detectado')
                ->body("La guía $guide es un reingreso en esta bodega.")
                ->success()
                ->send();
        }

        $this->motherGuides[] = $guide;
        $this->motherGuidesTimes[$guide] = now()->toDateTimeString();
    }

    public function addChildGuide(string $guide, $currentWarehouseId)
    {
        $guide = trim($guide);

        if (!$guide) return;

        if (in_array($guide, $this->childGuides)) {
            Notification::make()
                ->title('Guía ya escaneada')
                ->body('La guía ' . $guide . ' ya ha sido escaneada.')
                ->warning()
                ->persistent()
                ->send();
            return;
        }

        // Validar reingreso
        $estado = $this->validarGuiaParaIngreso(str_replace('GU0', '', $guide), $currentWarehouseId);
        Logger($estado);

        if ($estado === 'YA_INGRESO_NO_SALIO') {
            Notification::make()
                ->title('Ingreso no permitido')
                ->body("La guía $guide ya fue ingresada en esta bodega y no ha salido.")
                ->danger()
                ->persistent()
                ->send();
            return;
        }

        if ($estado === 'INGRESO_OTRA_NO_SALIO') {
            Notification::make()
                ->title('Ingreso no permitido')
                ->body("La guía $guide fue ingresada en otra bodega y no ha salido de allí.")
                ->danger()
                ->persistent()
                ->send();
            return;
        }

        // Si es 'OK' o 'REINGRESO', se permite agregar
        if ($estado === 'REINGRESO') {
            Notification::make()
                ->title('Reingreso detectado')
                ->body("La guía $guide es un reingreso en esta bodega.")
                ->success()
                ->send();
        }

        $this->childGuides[] = $guide;
        $this->childGuidesTimes[$guide] = now()->toDateTimeString(); // Guarda la hora
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

        foreach ($this->scannedGuides as $guide) {
            $isReentry = $this->validarGuiaParaIngreso(str_replace('GU0', '', $guide), $this->record->warehouse_id) === 'REINGRESO' ? 1 : 0;
            Logger("Guía: $guide es $isReentry");

            $this->record->guides()->create([
                'guide_number' => str_replace('GU0', '', $guide),
                'mother_guide' => in_array($guide, $this->motherGuides) ? $guide : null,
                'child_guide' => in_array($guide, $this->childGuides) ? $guide : null,
                'scanned_at' => $this->scannedGuidesTimes[$guide],
                'is_reentry' => $isReentry, // Aquí guardas si es reingreso o no
            ]);
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
}
