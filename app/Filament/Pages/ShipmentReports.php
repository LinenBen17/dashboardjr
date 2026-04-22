<?php

namespace App\Filament\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class ShipmentReports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.shipment-reports';

    public $from;
    public $to;
    public $cliente;
    public $agrupacion = 'destino';
    // public $tipo = 'detallado';
    public $localidad = 'Guatemala';
    // public $contraEntrega = false;

    public $data;

    public function getAllData()
    {
        $query = DB::table('shipment_entries')
            ->leftJoin('towns', 'shipment_entries.town_id', '=', 'towns.id')
            ->leftJoin('payment_methods', 'shipment_entries.payment_method_id', '=', 'payment_methods.id')
            ->leftJoin('shipment_manifests', 'shipment_entries.shipment_manifest_id', '=', 'shipment_manifests.id')
            ->leftJoin('users', 'shipment_entries.created_by', '=', 'users.id')
            ->where('shipment_entries.created_at', '>=', $this->from)
            ->where('shipment_entries.created_at', '<=', $this->to)
            ->select(
                'shipment_entries.id',
                'mother',
                'sender_code',
                'sender_name',
                'sender_address',
                'sender_phone',
                'receiver_code',
                'receiver_name',
                'receiver_address',
                'receiver_phone',
                'prefix_origin',
                'prefix_destination',
                'towns.name as town_destination',
                'product_description',
                'pieces',
                'unit_price',
                'sender_total',
                'receiver_total',
                'total',
                'date_guide',
                'payment_methods.name as payment_method',
                'shipment_manifests.manifest_code as manifest_code',
                'users.username as created_by',
            );

        return $query->get();
    }

    public function verEnPantalla()
    {
        if (!$this->from || !$this->to) {
            Notification::make()
                ->title('Error')
                ->body('Por favor, selecciona un rango de fechas válido.')
                ->danger()
                ->send();
            return;
        }

        if ($this->agrupacion === 'destino') {
            $data = $this->getAllData()
                ->groupBy('town_destination');
        } else {
            $data = $this->getAllData()
                ->groupBy('receiver_code');
        }

        $this->data = $data;

        $this->dispatch('open-modal', id: 'reporte-modal');
    }

    public function exportarExcel()
    {
        Logger('Exportando reporte de envíos a Excel');
    }
}
