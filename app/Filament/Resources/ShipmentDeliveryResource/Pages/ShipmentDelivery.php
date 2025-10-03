<?php

namespace App\Filament\Resources\ShipmentDeliveryResource\Pages;

use App\Filament\Resources\ShipmentDeliveryResource;
use App\Models\ShipmentDelivery as ModelsShipmentDelivery;
use App\Models\ShipmentEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;

class ShipmentDelivery extends Page
{
    protected static string $resource = ShipmentDeliveryResource::class;

    protected static ?string $title = 'Entrega de Envíos';

    protected static string $view = 'filament.resources.shipment-delivery-resource.pages.shipment-delivery';

    public $shipment_entry_guide;
    public $received_by_name;
    public $received_by_document;
    public $signed = false;
    public $status = 'pending';
    public $observations;

    public $user;

    public function mount()
    {
        $this->received_by_name = '';
        $this->received_by_document = '';
        $this->signed = false;
        $this->status = 'pending';
        $this->observations = '';
        $this->shipment_entry_guide = '';

        $this->user = Auth::user();
    }

    public function confirmSave()
    {
        $shipment_entry_id = ShipmentEntry::where('mother', $this->shipment_entry_guide)->first();
        $shipment_delivery_exists = ModelsShipmentDelivery::where('shipment_entry_id', $shipment_entry_id)->first();

        if ((empty($this->received_by_name) && empty($this->received_by_document))) {
            Notification::make()
                ->title('Error: Almenos un campo de quien recibe es obligatorio.')
                ->danger()
                ->send();
            $this->dispatch('restartFocus');
            return;
        }

        if ($shipment_delivery_exists) {
            $shipment_delivery_exists->update([
                'received_by_name' => $this->received_by_name,
                'received_by_document' => $this->received_by_document,
                'signed' => $this->signed,
                'observations' => $this->observations ?? null,
                'status' => 'delivered',
            ]);

            Notification::make()
                ->title('Datos de entrega actualizados con éxito.')
                ->success()
                ->send();

            $this->mount();
            $this->dispatch('restartFocus');
            Logger("RESTART");

            return;
        }

        $shipment_delivery = ModelsShipmentDelivery::create([
            'shipment_entry_id' => $shipment_entry_id->id,
            'received_by_name' => $this->received_by_name,
            'received_by_document' => $this->received_by_document,
            'signed' => $this->signed,
            'observations' => $this->observations ?? null,
            'status' => 'delivered',
            'created_by' => $this->user->id,
        ]);

        Notification::make()
            ->title('Datos de entrega registrados con éxito.')
            ->success()
            ->send();

        $this->dispatch('restartFocus');
        $this->mount();
    }
}
