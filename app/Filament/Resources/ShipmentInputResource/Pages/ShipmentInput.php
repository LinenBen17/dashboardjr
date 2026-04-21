<?php

namespace App\Filament\Resources\ShipmentInputResource\Pages;

use App\Filament\Resources\ShipmentInputResource;
use App\Models\CashOnDelivery;
use App\Models\Customer;
use App\Models\ShipmentEntry;
use App\Models\ShipmentEntryChild;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Log\Logger;

class ShipmentInput extends Page
{
    protected static string $resource = ShipmentInputResource::class;

    protected static string $view = 'filament.resources.shipment-input-resource.pages.shipment-input';
    public $no_guide_user;
    public $manifest_code;
    public $date_guide;
    public $payment_method_id;
    public $sender_total;
    public $receiver_total;
    public $total;

    public $sender_code;
    public $sender_name;
    public $sender_address;
    public $sender_phone;
    public $receiver_code;
    public $receiver_name;
    public $receiver_address;
    public $receiver_phone;

    public $prefix_origin;
    public $prefix_destination;
    public $town_id;

    public $productos = [];
    public $newProduct = [
        'product_id' => '',
        'pieces' => '1',
        'product_description' => '',
        'unit_price' => '40.00',
        'subtotal' => '0.00',
    ];

    public $newSpecialProducts = [];

    public $customer_data_prices = [];

    public $pce_data = [];

    public $payment_methods;
    public array $childGuides = [];
    public int $totalPieces = 0;
    public bool $link_child_later = false;
    public $record;

    public $municipios = [];

    public $statusPrinted = false;

    private function getProductIdByCode($code)
    {
        return DB::table('products')
            ->where('code', $code)
            ->value('id');
    }

    public function mount()
    {
        $this->date_guide = now()->format('Y-m-d'); // o Carbon::now()->format('Y-m-d')

        $this->prefix_origin = DB::table('departaments')
            ->where('id', Auth::user()->custom_fields['departament_id'])
            ->value('prefix');

        $this->payment_methods = DB::table('payment_methods')
            ->select('id', 'name')
            ->orderBy('id')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function openPCEModal()
    {
        $this->dispatch('open-modal', id: 'pceModal');
    }

    public function closePCEModal()
    {

        $this->dispatch('close-modal', id: 'pceModal');
    }

    public function addCODProduct()
    {
        if (empty($this->pce_data)) {
            return;
        }

        Logger($this->pce_data);

        $data = $this->pce_data;

        // Validaciones básicas (opcional pero recomendado)
        if (
            empty($data['pce_amount']) ||
            empty($data['pce_pieces'])
        ) {
            Notification::make()
                ->title('Datos incompletos en Contra Entrega')
                ->warning()
                ->send();
            return;
        }

        $calc = $this->calculateCE($data);

        $pieces = (int) $data['pce_pieces'];

        // 🔥 AQUÍ defines el precio del producto
        // Lo que paga el destinatario normalmente
        $total = $calc['total_receiver'] + $calc['commission_amount'];

        $unitPrice = $total / $pieces;

        // 🔥 Crear producto tipo COD
        $productId = $this->getProductIdByCode(1);

        $this->productos[] = [
            'product_id' => $productId,
            'pieces' => $pieces,
            'product_description' => 'PAGO CONTRA ENTREGA',
            'unit_price' => $data['pce_amount'],
            'subtotal' => $total,
        ];

        // Cerrar modal
        $this->closePCEModal();

        // Recalcular totales del envío
        $this->calculateTotals();
        // UX
        $this->dispatch('focus-product-input');
    }

    public function openSpecialRatesModal()
    {
        $this->hydrateSpecialProducts();
        $this->dispatch('open-modal', id: 'specialRatesModal');
    }

    public function closeSpecialRatesModal()
    {
        $this->dispatch('close-modal', id: 'specialRatesModal');
    }

    public function setClienteRemitente($code)
    {
        $this->sender_code = $code;

        $cliente = Customer::where('code', $code)->first();

        if ($cliente) {
            $this->sender_name = $cliente->name;
            $this->sender_address = $cliente->address;
            $this->sender_phone = $cliente->phone;
        }
    }

    public function setClienteDestinatario($code)
    {
        $this->receiver_code = $code;

        $cliente = Customer::where('code', $code)->first();

        if ($cliente) {
            $this->receiver_name = $cliente->name;
            $this->receiver_address = $cliente->address;
            $this->receiver_phone = $cliente->phone;
        }
    }

    public function openConsultGuides()
    {
        $this->dispatch('open-modal', id: 'consultGuides');
    }

    public function getCustomerID($code)
    {
        return DB::table('customers')
            ->where(function ($query) use ($code) {
                $query->where('code', $code)
                    ->orWhere('code', 'like', '%-' . $code);
            })
            ->value('id');
    }

    public function addProduct()
    {
        $this->newProduct['subtotal'] =
            $this->newProduct['pieces'] * $this->newProduct['unit_price'];

        $productId = $this->getProductIdByCode($this->newProduct['product_id']);

        if (!$productId) {
            Notification::make()
                ->title('Producto inválido')
                ->danger()
                ->send();
            return;
        }

        $this->newProduct['product_id'] = $productId;

        $this->productos[] = $this->newProduct;

        $this->calculateTotals();

        $this->newProduct = [
            'product_id' => '',
            'pieces' => '1',
            'product_description' => '',
            'unit_price' => '40.00',
            'subtotal' => '0.00',
        ];

        $this->dispatch('focus-product-input');
    }

    public function removeProduct($index)
    {
        Logger("Eliminando producto en el índice: " . $index);
        unset($this->productos[$index]);
        $this->productos = array_values($this->productos);

        $this->calculateTotals();

        $this->dispatch('focus-product-input');
    }

    public function hydrateSpecialProducts()
    {
        $this->newSpecialProducts = [];

        foreach ($this->customer_data_prices as $customer) {
            foreach ($customer['special_rates'] as $rate) {
                $id = $rate['product_id'];

                $this->newSpecialProducts[$id] = [
                    'selected' => false,
                    'product_code' => $rate['product_code'],
                    'product_description' => $rate['product_name'],
                    'pieces' => 1,
                    'unit_price' => $rate['special_price'],
                ];
            }
        }
    }

    public function addProductSpecial()
    {
        foreach ($this->newSpecialProducts as $productId => $product) {

            if (empty($product['selected'])) continue;
            $subtotal = $product['pieces'] * $product['unit_price'];

            $this->productos[] = [
                'product_id' => $productId,
                'pieces' => $product['pieces'],
                'product_description' => $product['product_description'],
                'unit_price' => $product['unit_price'],
                'subtotal' => $subtotal,
            ];
        }

        $this->calculateTotals();

        $this->closeSpecialRatesModal();

        $this->newSpecialProducts = [];

        $this->dispatch('focus-product-input');
    }

    public function calculateTotals()
    {
        Logger($this->productos);
        $this->totalPieces = 0;
        $this->total = 0;

        foreach ($this->productos as $product) {
            $this->totalPieces += (int) $product['pieces'];
            $this->total += (float) $product['subtotal'];
        }
    }

    public function saveCOData($shipmentId)
    {
        logger($this->pce_data);
        $data = $this->pce_data;

        $calc = $this->calculateCE($data);

        CashOnDelivery::create([
            'shipment_entry_id' => $shipmentId,
            'no_pce' => $data['no_pce'],
            'amount' => $data['pce_amount'] * $data['pce_pieces'],
            'pieces' => $data['pce_pieces'],
            'shipment_price' => $data['pce_shipment_price'] * $data['pce_pieces'],

            'shipment_paid_by' => 'receiver',
            'include_commission' => $data['commission_paid_by'] ?? false,

            // resultados calculados
            'commission_amount' => $calc['commission_amount'],
            'commission_rate' => $calc['commission_rate'],
            'total_receiver' => $calc['total_receiver'],
            'total_sender' => $calc['total_sender'],
            'per_piece_receiver' => $calc['per_piece_receiver'],
            'per_piece_sender' => $calc['per_piece_sender'],
        ]);
    }

    public function calculateCE($data)
    {
        $producto = (float) ($data['pce_amount'] ?? 0);
        $piezas = (int) ($data['pce_pieces'] ?? 1);
        $envio = (float) ($data['pce_shipment_price'] ?? 0) * $piezas;

        $shipmentPaidBy = $data['shipment_paid_by'] ?? 'receiver';
        $includeCommission = $data['commission_paid_by'] ?? false;

        if ($piezas <= 0) $piezas = 1;

        $commissionRate = 0.05;
        $comision = $producto * $commissionRate * $piezas;

        $totalDestinatario = $producto;
        $totalRemitente = $producto;

        return [
            'commission_amount' => $comision,
            'total_receiver' => $envio,
            'total_sender' => 0,
            'per_piece_receiver' => $totalDestinatario / $piezas,
            'per_piece_sender' => $totalRemitente / $piezas,
            'commission_rate' => $commissionRate,
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

    public function confirmChilds()
    {
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

    public function confirmSave()
    {
        if ($this->link_child_later) {
            Logger("El valor de link_child_later es: true");
        } else {
            Logger("El valor de link_child_later es: false");
        }
        $this->totalPieces = 0;
        // Get total pieces from products
        foreach ($this->productos as $product => $value) {
            if (isset($value['pieces'])) {
                $this->totalPieces += $value['pieces'];
            }
        }

        if ($this->totalPieces > 1) {
            $this->dispatch('open-modal', id: 'childGuides');
        } else {
            $this->link_child_later = true;
            $this->save();
        }
    }

    public function save()
    {
        Logger("Guardando envío...");
        Logger($this->all());
        try {
            $this->validate([
                'no_guide_user' => 'required|numeric',
                'date_guide' => 'required|date',
                'payment_method_id' => 'required',
                'sender_name' => 'required|string|max:255',
                'sender_address' => 'required|string|max:255',
                'sender_phone' => 'nullable|string|max:20',
                'receiver_name' => 'required|string|max:255',
                'receiver_address' => 'required|string|max:255',
                'receiver_phone' => 'nullable|string|max:20',
                'prefix_origin' => 'required|string|max:3',
                'prefix_destination' => 'required|string|max:3',
                'town_id' => 'required',
            ]);
        } catch (ValidationException $e) {
            Logger('Error de validación: ' . $e->getMessage());
            // Si prefieres mostrar errores como notificación (uno solo general)
            Notification::make()
                ->title('Faltan campos obligatorios')
                ->body('Por favor completa todos los campos requeridos.')
                ->danger()
                ->send();

            return;
        }
        Logger("Validación exitosa, procediendo a guardar...");

        // Obtener usuario autenticado y sus custom fields
        $user = Filament::auth()->user();
        $custom = $user->custom_fields ?? [];

        // Obtener el id del cliente remiente o destinatario
        $senderCustomerId = $this->getCustomerID($this->sender_code);
        $receiverCustomerId = $this->getCustomerID($this->receiver_code);

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
            ->where('id', $this->payment_method_id)
            ->value('name');

        //Asignando total de pieces
        $pieces = $this->totalPieces;

        //Dividiendo Día, Mes y Año
        $dateGuide = Carbon::parse($this->date_guide);

        //obtener manifiesto id
        $shipment_manifest_id = DB::table('shipment_manifests')
            ->where('manifest_code', $this->manifest_code)
            ->value('id');

        $dia = $dateGuide->day;
        $mes = $dateGuide->month;
        $anio = $dateGuide->year;

        try {
            if ($this->link_child_later) {
                $this->record = ShipmentEntry::create([
                    'mother' => $this->no_guide_user,
                    'sender_code' => $senderCustomerId ?? null,
                    'sender_name' => $this->sender_name,
                    'sender_address' => $this->sender_address,
                    'sender_phone' => $this->sender_phone,
                    'receiver_code' => $receiverCustomerId ?? null,
                    'receiver_name' => $this->receiver_name,
                    'receiver_address' => $this->receiver_address,
                    'receiver_phone' => $this->receiver_phone,
                    'prefix_origin' => $this->prefix_origin,
                    'prefix_destination' => $this->prefix_destination,
                    'town_id' => $this->town_id,
                    'product_id' => $productId,
                    'product_description' => $productDescription,
                    'pieces' => $this->totalPieces,
                    'unit_price' => ($this->totalPieces == 1) ? $this->total : $this->productos[0]['unit_price'],
                    'sender_total' => $this->sender_total ?? 0,
                    'receiver_total' => $this->receiver_total ?? 0,
                    'total' => $this->total,
                    'date_guide' => $this->date_guide,
                    'payment_method_id' => $this->payment_method_id,
                    'shipment_manifest_id' => $shipment_manifest_id ?? null,
                    'created_by' => $user->id, // Guarda el ID del usuario que crea la entrada
                ]);
                $this->record->logSnapshot(
                    'created',
                    'Guía creada sin guías hijas enlazadas por el usuario: ' . $user->custom_fields['user_name'] . '. Horario: ' . Carbon::now()->format('d/m/Y H:i:s'),
                );
            } else if ($this->link_child_later == false) {
                $this->record = ShipmentEntry::create([
                    'mother' => $this->no_guide_user,
                    'sender_code' => $senderCustomerId ?? null,
                    'sender_name' => $this->sender_name,
                    'sender_address' => $this->sender_address,
                    'sender_phone' => $this->sender_phone,
                    'receiver_code' => $receiverCustomerId ?? null,
                    'receiver_name' => $this->receiver_name,
                    'receiver_address' => $this->receiver_address,
                    'receiver_phone' => $this->receiver_phone,
                    'prefix_origin' => $this->prefix_origin,
                    'prefix_destination' => $this->prefix_destination,
                    'town_id' => $this->town_id,
                    'product_id' => $productId,
                    'product_description' => $productDescription,
                    'pieces' => $this->totalPieces,
                    'unit_price' => ($this->totalPieces == 1) ? $this->total : $this->productos[0]['unit_price'],
                    'sender_total' => $this->sender_total ?? 0,
                    'receiver_total' => $this->receiver_total ?? 0,
                    'total' => $this->total,
                    'date_guide' => $this->date_guide,
                    'payment_method_id' => $this->payment_method_id,
                    'shipment_manifest_id' => $shipment_manifest_id ?? null,
                    'created_by' => $user->id, // Guarda el ID del usuario que crea la entrada
                ]);

                $this->record->logSnapshot(
                    'created',
                    'Guía creada con guías hijas enlazadas por el usuario: ' . $user->custom_fields['user_name'] . 'Horario: ' . Carbon::now()->format('d/m/Y H:i:s'),
                );

                // Recorrer los productos del envío
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

                // Como la guía madre cuenta como una pieza, eliminamos la primera
                array_shift($productEntries);

                // Validar que existan los productos antes de crear las guías hijas
                foreach ($productEntries as $entry) {
                    if (!DB::table('products')->where('id', $entry['product_id'])->exists()) {
                        Notification::make()
                            ->title('Error al guardar guías hijas')
                            ->body('El producto con ID ' . $entry['product_id'] . ' no existe. Por favor revisa los productos antes de guardar.')
                            ->danger()
                            ->send();
                        return;
                    }
                }

                // Ahora creamos cada guía hija
                foreach ($this->childGuides as $index => $childGuide) {
                    $entry = $productEntries[$index] ?? null;

                    if ($entry) {
                        ShipmentEntryChild::create([
                            'shipment_entry_id' => $this->record->id,
                            'child_guide' => $childGuide,
                            'product_id' => $entry['product_id'],
                            'price' => $entry['price'],
                        ]);
                    }
                }
            }

            if (!empty($this->pce_data)) {
                $this->saveCOData($this->record->id);
            }

            Notification::make()
                ->title('Envío creado exitosamente')
                ->success()
                ->send();

            //Resetea todo

            //Limpia variables
            $this->childGuides = [];
            $this->totalPieces = 0;

            $this->no_guide_user = '';
            $shipment_manifest_id = null;
            $this->manifest_code = '';
            $this->date_guide = now()->format('Y-m-d');
            $this->payment_method_id = 1;

            $this->sender_total = '0.00';
            $this->receiver_total = '0.00';
            $this->total = '0.00';

            $this->sender_code = '';
            $this->sender_name = '';
            $this->sender_address = '';
            $this->sender_phone = '';
            $this->receiver_code = '';
            $this->receiver_name = '';
            $this->receiver_address = '';
            $this->receiver_phone = '';

            $this->prefix_destination = '';
            $this->town_id = '';

            $this->productos = [];

            $this->dispatch('restartFocus');
        } catch (\Throwable $th) {
            Notification::make()
                ->title('Hubo un error al guardar')
                ->body($th->getMessage())
                ->warning()
                ->send();
            return;
        }
    }

    public function notificationJs($title, $message, $type)
    {
        Notification::make()
            ->title($title)
            ->body($message)
            ->{$type}()
            ->send();
    }
}
