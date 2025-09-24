<?php

namespace App\Filament\Resources\ShipmentManifestResource\Pages;

use App\Filament\Resources\ShipmentManifestResource;
use App\Models\Agency;
use App\Models\ShipmentManifest as ModelsShipmentManifest;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use League\Csv\Query\Row;

class ShipmentManifest extends Page
{
    protected static string $resource = ShipmentManifestResource::class;

    protected static ?string $title = 'Generar Manifiestos Auditables';

    protected static string $view = 'filament.resources.shipment-manifest-resource.pages.shipment-manifest';

    // valores seleccionados
    public $manifest_code;
    public $date;
    public $route_id; // ID seleccionado
    public $agency_origin_id; // ID seleccionado
    public $agency_destination_id; // ID seleccionado

    // listas de opciones
    public $routes = [];
    public $agencies_origin = [];
    public $manifested_guides_id = [];
    public $manifest_repeated = false;

    public function mount()
    {
        $this->date = now()->format('Y-m-d');

        // cargar arrays en propiedades separadas
        $this->routes = DB::table('routes')
            ->orderBy('prefix', 'asc')
            ->pluck('prefix', 'id')
            ->toArray();

        $this->agencies_origin = Agency::where('id', Auth::user()->custom_fields['agency_id'])
            ->pluck('name', 'id')
            ->toArray();

        // inicializar valores seleccionados si aplica
        $this->agency_origin_id = array_key_first($this->agencies_origin);
    }

    public function unlinkGuides()
    {
        $get_register_manifest = DB::table('shipment_manifests')
            ->where('manifest_code', $this->manifest_code)
            ->exists();

        if (!$get_register_manifest) {
            Notification::make()
                ->title('Estas guías no están asociadas a ningún manifiesto')
                ->danger()
                ->send();
            return;
        }

        DB::table('shipment_manifests')
            ->where('manifest_code', $this->manifest_code)
            ->delete();

        DB::table('shipment_entries')
            ->whereIn('id', $this->manifested_guides_id)
            ->update(['shipment_manifest_id' => null]);

        $this->manifest_code = '';
        $this->manifested_guides_id = [];
        $this->mount();
        $this->dispatch('restartFocus');
        Notification::make()
            ->title('Manifiesto desasociado con éxito')
            ->success()
            ->send();
    }

    public function confirmSave()
    {
        $this->manifest_repeated = DB::table('shipment_manifests')
            ->where('manifest_code', $this->manifest_code)
            ->first();

        $notificationMessage = "";

        try {

            if ($this->manifest_repeated == false) {
                $shipment_manifest = ModelsShipmentManifest::create([
                    'manifest_code' => $this->manifest_code,
                    'date' => $this->date,
                    'route_id' => $this->route_id,
                    'agency_origin_id' => $this->agency_origin_id,
                    'agency_destination_id' => $this->agency_destination_id,
                ]);

                $notificationMessage = "Manifiesto creado con éxito: " . $this->manifest_code;
            } else {
                $shipment_manifest = $this->manifest_repeated;
                $notificationMessage = "Manifiesto actualizado con éxito: " . $this->manifest_code;
            }

            // Actualizar las guías seleccionadas para que pertenezcan al manifiesto creado
            $guides_to_manifest = DB::table('shipment_entries')
                ->whereIn('id', $this->manifested_guides_id)
                ->update(['shipment_manifest_id' => $shipment_manifest->id]);
        } catch (\Throwable $th) {
            Notification::make()
                ->title('Error al crear el manifiesto: ' . $this->manifest_repeated)
                ->danger()
                ->send();
            return;
        }

        Notification::make()
            ->title($notificationMessage)
            ->success()
            ->send();

        $this->manifest_code = '';
        $this->manifested_guides_id = [];

        $this->mount();
        $this->dispatch('restartFocus');
    }
}
