<?php

namespace App\Filament\Resources\ShipmentManifestResource\Pages;

use App\Filament\Resources\ShipmentManifestResource;
use App\Models\Agency;
use App\Models\Product;
use App\Models\ShipmentEntry;
use App\Models\ShipmentEntryChild;
use App\Models\ShipmentManifest as ModelsShipmentManifest;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
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

    // campos para modificar guías
    public $record;
    public $payment_methods;

    public $no_guide_user_update;
    public $manifest_code_update;
    public $date_guide_update;
    public $payment_method_id_update;
    public $sender_total_update;
    public $receiver_total_update;
    public $total_update;

    public $sender_code_update;
    public $sender_name_update;
    public $sender_address_update;
    public $sender_phone_update;
    public $receiver_code_update;
    public $receiver_name_update;
    public $receiver_address_update;
    public $receiver_phone_update;

    public $prefix_origin_update;
    public $prefix_destination_update;
    public $town_id_update;

    public $updateData = [];

    public bool $guideIncomplete = false;
    public bool $guidePartial = false;

    public $productos = [
        [
            'product_id' => '',
            'pieces' => '',
            'product_description' => '',
            'unit_price' => '',
            'subtotal' => '',
        ]
    ];

    public array $childGuidesData = [];
    public array $childGuides = [];
    public int $totalPieces = 0;
    public bool $link_child_later = false;
    public $municipios = [];

    public $user;

    public function mount()
    {
        $this->user = Auth::user();

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

        $this->payment_methods = DB::table('payment_methods')
            ->select('id', 'name')
            ->orderBy('id')
            ->pluck('name', 'id')
            ->toArray();
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

        $guides = ShipmentEntry::whereIn('id', $this->manifested_guides_id)->get();

        foreach ($guides as $guide) {
            $guide->update([
                'shipment_manifest_id' => null,
            ]);

            $guide->logSnapshot(
                'unmanifested',
                'Guía retirada del manifiesto ' . $this->manifest_code .
                    ' por ' . Filament::auth()->user()->custom_fields['user_name'] .
                    ' el ' . Carbon::now()->format('d/m/Y H:i:s')
            );
        }

        // Ahora sí eliminamos el manifiesto
        DB::table('shipment_manifests')
            ->where('manifest_code', $this->manifest_code)
            ->delete();

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
            $guides = ShipmentEntry::whereIn('id', $this->manifested_guides_id)->get();

            foreach ($guides as $guide) {
                $guide->update([
                    'shipment_manifest_id' => $shipment_manifest->id,
                ]);

                $guide->logSnapshot(
                    'manifested',
                    'Guía asignada al manifiesto ' . $shipment_manifest->manifest_code .
                        ' por ' . Filament::auth()->user()->custom_fields['user_name'] .
                        ' el ' . Carbon::now()->format('d/m/Y H:i:s')
                );
            }
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

    // Funciones para modificar guías
    public function buscarGuia($shipment_entry_id)
    {
        // Reset
        $this->childGuidesData = [];
        $this->childGuides = [];
        $this->productos = [];
        $this->totalPieces = 0;
        $this->guideIncomplete = false;
        $this->guidePartial = false;

        if (!$shipment_entry_id) {
            return;
        }

        $shipment = ShipmentEntry::find($shipment_entry_id);

        if (!$shipment) {
            return;
        }

        $children = ShipmentEntryChild::where('shipment_entry_id', $shipment_entry_id)->get();

        $expectedPieces   = (int) $shipment->pieces;       // total piezas (madre + hijas)
        $expectedChildren = max(0, $expectedPieces - 1);   // hijas esperadas
        $childrenCount    = $children->count();

        /*
    |--------------------------------------------------------------------------
    | CASO 1: Guía simple (solo madre)
    |--------------------------------------------------------------------------
    */
        if ($expectedPieces === 1 && $childrenCount === 0) {
            $this->productos[] = [
                'product_id' => $shipment->product_id,
                'pieces' => 1,
                'product_description' => Product::find($shipment->product_id)->name ?? 'N/A',
                'unit_price' => $shipment->unit_price,
                'subtotal' => $shipment->unit_price,
                'is_placeholder' => false,
            ];

            $this->totalPieces = 1;
            return;
        }

        /*
    |--------------------------------------------------------------------------
    | CASO 2: Guía multipieza SIN hijas (INCOMPLETA)
    |--------------------------------------------------------------------------
    */
        if ($expectedPieces > 1 && $childrenCount === 0) {
            $this->guideIncomplete = true;

            // Placeholder global
            $this->productos[] = [
                'product_id' => null,
                'pieces' => $expectedPieces,
                'product_description' => 'Desglose pendiente (guías hijas no enlazadas)',
                'unit_price' => null,
                'subtotal' => $shipment->total,
                'is_placeholder' => true,
            ];

            $this->totalPieces = $expectedPieces;

            Notification::make()
                ->title('Guías Hijas No Enlazadas')
                ->body("Esta guía tiene {$expectedPieces} piezas, pero no se ha enlazado ninguna guía hija.")
                ->warning()
                ->send();

            return;
        }

        /*
    |--------------------------------------------------------------------------
    | CASO 3: Guía multipieza PARCIAL
    |--------------------------------------------------------------------------
    */
        if ($childrenCount < $expectedChildren) {
            $this->guidePartial = true;

            Notification::make()
                ->title('Guías Hijas Faltantes')
                ->body(
                    "Esta guía tiene {$expectedPieces}, pero solo se han enlazado {$childrenCount} guías hijas."
                )
                ->warning()
                ->send();
        }

        /*
    |--------------------------------------------------------------------------
    | CASO 4: Procesar guías hijas
    |--------------------------------------------------------------------------
    */
        foreach ($children as $child) {
            $this->childGuidesData[] = [
                'id' => $child->id,
                'child_guide' => $child->child_guide,
                'product_id' => $child->product_id,
                'price' => $child->price,
                'updated_at' => $child->updated_at,
            ];

            $this->childGuides[] = $child->child_guide;
        }

        // Agrupar productos de HIJAS
        $grouped = [];

        foreach ($this->childGuidesData as $childGuide) {
            $key = $childGuide['product_id'] . '_' . $childGuide['price'];

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'product_id' => $childGuide['product_id'],
                    'pieces' => 1,
                    'product_description' => Product::find($childGuide['product_id'])->name ?? 'N/A',
                    'unit_price' => $childGuide['price'],
                    'subtotal' => $childGuide['price'],
                    'is_placeholder' => false,
                ];
            } else {
                $grouped[$key]['pieces']++;
                $grouped[$key]['subtotal'] += $childGuide['price'];
            }
        }

        // HIJAS primero
        $this->productos = array_values($grouped);

        // AGREGAR SIEMPRE LA MADRE (como en tu código original)
        $this->productos[] = [
            'product_id' => $shipment->product_id,
            'pieces' => 1,
            'product_description' => Product::find($shipment->product_id)->name ?? 'N/A',
            'unit_price' => $shipment->unit_price,
            'subtotal' => $shipment->unit_price,
            'is_placeholder' => false,
        ];

        // Total real de piezas
        $this->totalPieces = 1 + $childrenCount;
    }

    public function setValuesUpdateGuide()
    {
        // Aplicar los datos del array updateData (asignado desde Javscript) a las propiedades correspondientes
        foreach ($this->updateData as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function confirmSaveUpdateGuide()
    {
        $this->setValuesUpdateGuide();

        $this->totalPieces = 0;
        // Get total pieces from products
        foreach ($this->productos as $product => $value) {
            if (isset($value['pieces'])) {
                $this->totalPieces += $value['pieces'];
            }
        }

        if ($this->totalPieces > 1) {
            Logger("ABRIENDO MODAL DE GUIAS HIJAS...");
            $this->dispatch('open-modal', id: 'childGuides');
            logger($this->childGuides);
        } else {
            $this->link_child_later = true;
            $this->save();
        }
    }

    public function confirmChilds()
    {
        Logger("CONFIRMANDO GUIAS HIJAS...");
        Logger(count($this->childGuides));


        $this->setValuesUpdateGuide();
        if ($this->link_child_later) {
            Logger('Enlazar guías hijas más tarde');
            $this->save();
            $this->dispatch('close-modal', id: 'childGuides');
            return;
        } elseif (count($this->childGuides) < ($this->totalPieces - 1) && !$this->link_child_later) {
            Notification::make()
                ->title('Guías hijas incompletas')
                ->body('Es necesario enlazar ' . ($this->totalPieces - 1) . ' guías hijas. ' . count($this->childGuides) . ' guías hijas actualmente enlazadas.')
                ->warning()
                ->send();
            return;
        } elseif (count($this->childGuides) == ($this->totalPieces - 1)  && !$this->link_child_later) {
            $this->save();
            $this->dispatch('close-modal', id: 'childGuides');
        }
    }

    public function save()
    {
        try {
            $this->validate([
                'no_guide_user_update' => 'required|numeric',
                'date_guide_update' => 'required|date',
                'payment_method_id_update' => 'required',
                'sender_name_update' => 'required|string|max:255',
                'sender_address_update' => 'required|string|max:255',
                'sender_phone_update' => 'nullable|string|max:20',
                'receiver_name_update' => 'required|string|max:255',
                'receiver_address_update' => 'required|string|max:255',
                'receiver_phone_update' => 'nullable|string|max:20',
                'prefix_origin_update' => 'required|string|max:3',
                'prefix_destination_update' => 'required|string|max:3',
                'town_id_update' => 'required',
            ]);
        } catch (ValidationException $e) {
            // Si prefieres mostrar errores como notificación (uno solo general)
            Notification::make()
                ->title('Faltan campos obligatorios')
                ->body('Por favor completa todos los campos requeridos.')
                ->danger()
                ->send();
            return;
        }
        // Obtener usuario autenticado y sus custom fields
        $user = Filament::auth()->user();
        $custom = $user->custom_fields ?? [];

        // Obtener el id del cliente remiente o destinatariox
        $senderCustomerId = $this->getCustomerID($this->sender_code_update);
        $receiverCustomerId = $this->getCustomerID($this->receiver_code_update);

        // Establecer descripcion completa del pedido si tuviese más de 1 pieza o no
        $arrayProductDescription = [];

        foreach ($this->productos as $product => $value) {
            if (isset($value['product_description'])) {
                $arrayProductDescription[] = $value['product_description'];
            }
        }
        $productDescription = implode(',', array_unique($arrayProductDescription)) ?? null;

        //Establecer codigo en caso de que sea solo 1 pieza
        $productId = 0;
        if ($this->totalPieces == 1) {
            foreach ($this->productos as $product => $value) {
                if (isset($value['product_id'])) {
                    $productId = $value['product_id'];
                }
            }
        } else {
            $productId = $this->productos[0]['product_id'] ?? null;
        }

        //obtener la forma de pago
        $paymentMethodName = DB::table('payment_methods')
            ->where('id', $this->payment_method_id_update)
            ->value('name');

        //Asignando total de pieces
        $pieces = $this->totalPieces;

        //Dividiendo Día, Mes y Año
        $dateGuide = Carbon::parse($this->date_guide_update);

        $dia = $dateGuide->day;
        $mes = $dateGuide->month;
        $anio = $dateGuide->year;

        //Encontrar id del manifiesto si existe
        $shipmentManifestId = null;
        if (!empty($this->manifest_code_update)) {
            $shipmentManifestId = DB::table('shipment_manifests')
                ->where('manifest_code', $this->manifest_code_update)
                ->value('id');
        }

        try {
            if ($this->link_child_later) {

                Logger("ACTUALIZANDO GUIA MADRE CON HIJOS DESPUÉS");

                $this->record = ShipmentEntry::where('mother', $this->no_guide_user_update)->first();

                if (!$this->record) {
                    Notification::make()
                        ->title('Error: No se encontró la guía madre para actualizar.')
                        ->danger()
                        ->send();
                    return;
                }

                $dataToUpdate = [
                    'mother' => $this->no_guide_user_update,
                    'sender_code' => $senderCustomerId ?? null,
                    'sender_name' => $this->sender_name_update,
                    'sender_address' => $this->sender_address_update,
                    'sender_phone' => $this->sender_phone_update,
                    'receiver_code' => $receiverCustomerId ?? null,
                    'receiver_name' => $this->receiver_name_update,
                    'receiver_address' => $this->receiver_address_update,
                    'receiver_phone' => $this->receiver_phone_update,
                    'prefix_origin' => $this->prefix_origin_update,
                    'prefix_destination' => $this->prefix_destination_update,
                    'town_id' => $this->town_id_update,
                    'product_id' => $productId,
                    'product_description' => $productDescription,
                    'pieces' => $this->totalPieces,
                    'unit_price' => ($this->totalPieces == 1)
                        ? $this->total_update
                        : $this->productos[0]['unit_price'],
                    'sender_total' => $this->sender_total ?? 0,
                    'receiver_total' => $this->receiver_total ?? 0,
                    'total' => $this->total_update,
                    'date_guide' => $this->date_guide_update,
                    'payment_method_id' => $this->payment_method_id_update,
                    'no_manifest' => null,
                    'created_by' => $user->id,
                ];

                $this->record->updateFields($dataToUpdate);

                ShipmentEntryChild::where('shipment_entry_id', $this->record->id)->delete();
            } else {

                $this->record = ShipmentEntry::where('mother', $this->no_guide_user_update)->first();

                if (!$this->record) {
                    Notification::make()
                        ->title('Error: No se encontró la guía madre para actualizar.')
                        ->danger()
                        ->send();
                    return;
                }

                ShipmentEntryChild::where('shipment_entry_id', $this->record->id)->delete();

                $dataToUpdate = [
                    'mother' => $this->no_guide_user_update,
                    'shipment_manifest_id' => $shipmentManifestId ?? null,
                    'sender_code' => $senderCustomerId ?? null,
                    'sender_name' => $this->sender_name_update,
                    'sender_address' => $this->sender_address_update,
                    'sender_phone' => $this->sender_phone_update,
                    'receiver_code' => $receiverCustomerId ?? null,
                    'receiver_name' => $this->receiver_name_update,
                    'receiver_address' => $this->receiver_address_update,
                    'receiver_phone' => $this->receiver_phone_update,
                    'prefix_origin' => $this->prefix_origin_update,
                    'prefix_destination' => $this->prefix_destination_update,
                    'town_id' => $this->town_id_update,
                    'product_id' => $productId,
                    'product_description' => $productDescription,
                    'pieces' => $this->totalPieces,
                    'unit_price' => ($this->totalPieces == 1)
                        ? $this->total_update
                        : $this->productos[0]['unit_price'],
                    'sender_total' => $this->sender_total ?? 0,
                    'receiver_total' => $this->receiver_total ?? 0,
                    'total' => $this->total_update,
                    'date_guide' => $this->date_guide_update,
                    'payment_method_id' => $this->payment_method_id_update,
                    'no_manifest' => null,
                    'created_by' => $user->id,
                ];

                $this->record->updateFields($dataToUpdate);

                $productEntries = [];

                foreach ($this->productos as $product) {
                    $pieces = (int) $product['pieces'];
                    $unitPrice = (float) $product['unit_price'];
                    $productId = $product['product_id'];

                    for ($i = 0; $i < $pieces; $i++) {
                        $productEntries[] = [
                            'product_id' => $productId,
                            'price' => $unitPrice,
                        ];
                    }
                }

                array_shift($productEntries);

                foreach ($this->childGuides as $index => $childGuide) {
                    $entry = $productEntries[$index] ?? null;

                    if ($entry) {
                        ShipmentEntryChild::create([
                            'shipment_entry_id' => $this->record->id,
                            'child_guide' => is_array($childGuide) && isset($childGuide['child_guide'])
                                ? $childGuide['child_guide']
                                : $childGuide,
                            'product_id' => $entry['product_id'],
                            'price' => $entry['price'],
                        ]);
                    }
                }
            }

            Notification::make()
                ->title('Envío actualizado exitosamente')
                ->success()
                ->send();
        } catch (\Throwable $th) {
            Notification::make()
                ->title('Hubo un error al actualizar la guía')
                ->body($th->getMessage())
                ->warning()
                ->send();
            return;
        }

        //limpia variables
        $this->link_child_later = false;
        $this->childGuides = [];
        $this->childGuidesData = [];
        $this->totalPieces = 0;
        $this->productos = [];

        $this->no_guide_user_update = '';
        $this->manifest_code_update = '';
        $this->date_guide_update = '';
        $this->payment_method_id_update = '';
        $this->sender_total_update = '';
        $this->receiver_total_update = '';
        $this->total_update = '';
        $this->sender_code_update = '';
        $this->sender_name_update = '';
        $this->sender_address_update = '';
        $this->sender_phone_update = '';
        $this->receiver_code_update = '';
        $this->receiver_name_update = '';
        $this->receiver_address_update = '';
        $this->receiver_phone_update = '';
        $this->prefix_origin_update = '';
        $this->prefix_destination_update = '';
        $this->town_id_update = '';

        $this->dispatch('close-modal', id: 'updateGuides');
    }

    public function addProduct()
    {
        $this->productos[] = [
            'product_id' => '',
            'pieces' => '',
            'product_description' => '',
            'unit_price' => '',
            'subtotal' => '',
        ];
    }

    public function addChildGuide(string $guide)
    {
        $guide = trim($guide);

        if (!$guide) return;

        if (in_array($guide, $this->childGuides)) {
            // Si la guía ya está en la lista, no hacer nada (No devuelve notificacion porque no es necesario)
            return;
        }
        if (count($this->childGuides) >= ($this->totalPieces - 1)) {
            Notification::make()
                ->title('Máximo de guías hijas alcanzado')
                ->body('No se pueden agregar más de ' . $this->totalPieces - 1 . ' guías hijas.')
                ->warning()
                ->send();
            return;
        }
        $this->childGuides[] = $guide;
    }

    public function removeChildGuide($index)
    {
        $indexGuideChild = array_search($index, $this->childGuides);
        unset($this->childGuides[$indexGuideChild]);
        $this->childGuides = array_values($this->childGuides); // Reindexa el array
    }

    public function removeProduct($index)
    {
        unset($this->productos[$index]);
        $this->productos = array_values($this->productos); // Reindexa el array

        // recalcular totales
        $this->total_update = 0;
        $this->receiver_total_update = 0;
        $this->sender_total_update = 0;
        foreach ($this->productos as $product) {
            $this->total_update += $product['subtotal'] ?? 0;
        }
    }

    public function openUpdateGuides()
    {
        $this->dispatch('open-modal', id: 'updateGuides');
    }

    public function getCustomerID($code)
    {
        $customer_id = DB::table('customers')
            ->where('code', 'LIKE', '%-' . $code)
            ->value('id');

        return $customer_id;
    }
}
