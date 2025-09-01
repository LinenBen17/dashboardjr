<?php

namespace App\Filament\Resources\WarehouseIncomesResource\Pages;

use App\Filament\Resources\WarehouseIncomesResource;
use App\Models\WarehouseIncomeGuide;
use App\Models\WarehouseOutgoGuide;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\DB;

class EditWarehouseIncomes extends EditRecord
{
    protected static string $resource = WarehouseIncomesResource::class;

    public array $scannedGuides = [];
    public array $motherGuides = [];
    public array $childGuides = [];

    public array $scannedGuidesTimes = [];
    public array $motherGuidesTimes = [];
    public array $childGuidesTimes = [];

    public string $bodegaGuiaAlojada = '';

    //function to start
    public function mount($record): void
    {
        parent::mount($record);
        $this->getGuides();
    }

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
                    $this->bodegaGuiaAlojada = DB::table('warehouses')->where('id', $bodegaIngreso)->value('name');
                    return 'INGRESO_OTRA_NO_SALIO';
                } else {
                    return 'OK'; // Ya salió de otra bodega, ingreso válido aquí
                }
            }
        }

        return 'ERROR';
    }

    public function addMotherGuide(string $guide)
    {
        $guide = trim($guide);
        if (!$guide) return;

        if (in_array($guide, $this->motherGuides)) {
            Notification::make()
                ->title('Guía ya escaneada')
                ->body("La guía $guide ya ha sido escaneada.")
                ->warning()
                ->persistent()
                ->send();
            return;
        }

        // ✅ Validar reingreso
        $estado = $this->validarGuiaParaIngreso(str_replace('GU0', '', $guide), $this->record->warehouse_id);

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
                ->body("La guía $guide fue ingresada en $this->bodegaGuiaAlojada y no ha salido de allí.")
                ->danger()
                ->persistent()
                ->send();
            return;
        }

        if ($estado === 'REINGRESO') {
            Notification::make()
                ->title('Reingreso detectado')
                ->body("La guía $guide es un reingreso en esta bodega.")
                ->success()
                ->send();
        }

        // Si es OK o REINGRESO se permite agregar
        $this->motherGuides[] = $guide;
        $this->motherGuidesTimes[$guide] = now()->toDateTimeString();
    }


    public function addChildGuide(string $guide)
    {
        $guide = trim($guide);
        if (!$guide) return;

        if (in_array($guide, $this->childGuides)) {
            Notification::make()
                ->title('Guía ya escaneada')
                ->body("La guía $guide ya ha sido escaneada.")
                ->warning()
                ->persistent()
                ->send();
            return;
        }

        // ✅ Validar reingreso
        $estado = $this->validarGuiaParaIngreso(str_replace('GU0', '', $guide), $this->record->warehouse_id);

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

        if ($estado === 'REINGRESO') {
            Notification::make()
                ->title('Reingreso detectado')
                ->body("La guía $guide es un reingreso en esta bodega.")
                ->success()
                ->send();
        }

        // Si es OK o REINGRESO se permite agregar
        $this->childGuides[] = $guide;
        $this->childGuidesTimes[$guide] = now()->toDateTimeString();
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
            $isReentry = $this->validarGuiaParaIngreso(str_replace('GU0', '', $guide), $this->record->warehouse_id) === 'REINGRESO' ? 1 : 0;

            $this->record->guides()->create([
                'guide_number' => str_replace('GU0', '', $guide),
                'mother_guide' => in_array($guide, $this->motherGuides) ? $guide : null,
                'child_guide' => in_array($guide, $this->childGuides) ? $guide : null,
                'scanned_at' => $this->motherGuidesTimes[$guide] ?? $this->childGuidesTimes[$guide],
                'is_reentry' => $isReentry, // 👈 también en edición
            ]);
        }
    }

    public function getGuides()
    {
        $this->motherGuides = WarehouseIncomeGuide::where('warehouse_income_id', $this->record->id)
            ->whereNotNull('mother_guide')
            ->pluck('mother_guide')
            ->toArray();
        $this->motherGuidesTimes = WarehouseIncomeGuide::where('warehouse_income_id', $this->record->id)
            ->whereNotNull('mother_guide')
            ->pluck('scanned_at', 'mother_guide')
            ->toArray();
        $this->childGuides = WarehouseIncomeGuide::where('warehouse_income_id', $this->record->id)
            ->whereNotNull('child_guide')
            ->pluck('child_guide')
            ->toArray();
        $this->childGuidesTimes = WarehouseIncomeGuide::where('warehouse_income_id', $this->record->id)
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
                ->modalContent(fn() => view('filament.resources.warehouse_incomes.modal-guides', [
                    'motherGuides' => $this->motherGuides,
                    'childGuides' => $this->childGuides,
                ])),
            Action::make('Imprimir')
                ->label('Imprimir')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->extraAttributes(['target' => '_blank'])
                ->url(route('manifest_income_format', $this->record->id)),
        ];
    }
}
