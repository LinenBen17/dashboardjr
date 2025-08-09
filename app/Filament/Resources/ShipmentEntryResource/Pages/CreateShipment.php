<?php

namespace App\Filament\Resources\ShipmentEntryResource\Pages;

use App\Filament\Resources\ShipmentEntryResource;
use App\Models\ShipmentEntry;
use App\Models\ShipmentEntryChild;
use App\Models\Town;
use Carbon\Carbon;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class CreateShipment extends Page
{

    protected static string $resource = ShipmentEntryResource::class;

    protected static string $view = 'filament.resources.shipment-entry-resource.pages.create-shipment';

    protected static ?string $navigationLabel = 'Ingreso de Envíos';
    protected static ?string $modelLabel = 'Ingreso de Envío';

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';
    protected static ?string $label = 'Ingreso de Envíos';

    public $no_guide_user;
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

    public $productos = [
        [
            'product_id' => '',
            'pieces' => '',
            'product_description' => '',
            'unit_price' => '',
            'subtotal' => '',
        ]
    ];

    public $payment_methods;
    public array $childGuides = [];
    public int $totalPieces = 0;
    public bool $link_child_later = false;
    public $record;

    public $municipios = [];


    public function mount()
    {
        $this->no_guide_user = Auth::user()->custom_fields['serial_number'];

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

    public function openCustomersModal()
    {
        $this->dispatch('open-modal', id: 'customersModal');
    }

    public function closeCustomersModal()
    {
        $this->dispatch('close-modal', id: 'customersModal');
    }

    public function getCustomerID($code)
    {
        $customer_id = DB::table('customers')
            ->where('code', 'LIKE', '%-' . $code)
            ->value('id');

        return $customer_id;
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

    public function removeProduct($index)
    {
        unset($this->productos[$index]);
        $this->productos = array_values($this->productos); // Reindexa el array
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
            $this->save();
        }
    }

    public function save()
    {
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
            // Si prefieres mostrar errores como notificación (uno solo general)
            Notification::make()
                ->title('Faltan campos obligatorios')
                ->body('Por favor completa todos los campos requeridos.')
                ->danger()
                ->send();

            return;
        }

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
            $productId = null;
        }

        //obtener la forma de pago
        $paymentMethodName = DB::table('payment_methods')
            ->where('id', $this->payment_method_id)
            ->value('name');

        //Asignando total de pieces
        $pieces = $this->totalPieces;

        //Dividiendo Día, Mes y Año
        $dateGuide = Carbon::parse($this->date_guide);

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
                    'unit_price' => ($this->totalPieces == 1) ? $this->total : 0,
                    'sender_total' => $this->sender_total ?? 0,
                    'receiver_total' => $this->receiver_total ?? 0,
                    'total' => $this->total,
                    'date_guide' => $this->date_guide,
                    'payment_method_id' => $this->payment_method_id,
                    'no_manifest' => null,
                ]);
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
                    'unit_price' => ($this->totalPieces == 1) ? $this->total : 0,
                    'sender_total' => $this->sender_total ?? 0,
                    'receiver_total' => $this->receiver_total ?? 0,
                    'total' => $this->total,
                    'date_guide' => $this->date_guide,
                    'payment_method_id' => $this->payment_method_id,
                    'no_manifest' => null,
                ]);

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
            Notification::make()
                ->title('Envío creado exitosamente')
                ->success()
                ->send();

            //Calcula el siguiente número de guía madre
            $nextMother = ((int) $this->no_guide_user) + 1;

            //Actualiza el numero de madre
            $user       = Filament::auth()->user();
            $custom     = $user->custom_fields ?? [];
            $custom['serial_number'] = $nextMother;

            $user->custom_fields = $custom;
            $user->save();

            //Resetea todo y vuelve a llenar sólo mother

            $this->dispatch('print-guide', [
                'payment_method' => $paymentMethodName,
                'sender_name' => $this->sender_name,
                'sender_address' => $this->sender_address,
                'sender_phone' => $this->sender_phone,
                'prefix_origin' => $this->prefix_origin,
                'receiver_name' => $this->receiver_name,
                'receiver_address' => $this->receiver_address,
                'receiver_phone' => $this->receiver_phone,
                'prefix_destination' => $this->prefix_destination,
                'product_description' => $productDescription,
                'total' => $this->total,
                'sender_code' => $this->sender_code,
                'receiver_code' => $this->receiver_code,
                'pieces' => $this->totalPieces,
                'user' => $custom['user_name'],
                'dia' => $dia,
                'mes' => $mes,
                'anio' => $anio,
            ]);

            //Limpia variables
            $this->childGuides = [];
            $this->totalPieces = 0;

            $this->no_guide_user = $nextMother;
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

            $this->productos = [
                [
                    'product_id' => '',
                    'pieces' => '',
                    'product_description' => '',
                    'unit_price' => '',
                    'subtotal' => '',
                ]
            ];

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
}
