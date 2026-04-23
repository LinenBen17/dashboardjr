<?php

namespace App\Filament\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

use App\Exports\ShipmentEntriesExport;
use Maatwebsite\Excel\Facades\Excel;

class ShipmentReports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.shipment-reports';

    public $from;
    public $to;
    public $cliente;
    // public $agrupacion = 'destino';
    public $tipo = 'detallado';
    public $localidad = 'Todo';
    // public $contraEntrega = false;

    public $data;

    public function getAllData()
    {
        $fromFormatted = date('Y-m-d H:i:s', strtotime($this->from));
        $toFormatted = date('Y-m-d H:i:s', strtotime($this->to));

        $query = DB::table('shipment_entries')
            ->leftJoin('towns', 'shipment_entries.town_id', '=', 'towns.id')
            ->leftJoin('payment_methods', 'shipment_entries.payment_method_id', '=', 'payment_methods.id')
            ->leftJoin('shipment_manifests', 'shipment_entries.shipment_manifest_id', '=', 'shipment_manifests.id')
            ->leftJoin('users', 'shipment_entries.created_by', '=', 'users.id')
            ->whereBetween('shipment_entries.date_guide', [$fromFormatted, $toFormatted])
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

        $fromFormatted = date('Y-m-d H:i:s', strtotime($this->from));
        $toFormatted = date('Y-m-d H:i:s', strtotime($this->to));

        if ($this->localidad === 'Todo') {
            $query = DB::table('shipment_entries')
                ->leftJoin('payment_methods', 'shipment_entries.payment_method_id', '=', 'payment_methods.id')
                ->whereBetween('shipment_entries.date_guide', [$fromFormatted, $toFormatted])
                ->selectRaw("
                SUM(
                    CASE 
                        WHEN payment_methods.name = 'Por Cobrar' THEN COALESCE(total,0)
                        WHEN payment_methods.name NOT IN ('Contado','Por Cobrar','Crédito','Prepago') THEN COALESCE(receiver_total,0)
                        ELSE 0
                    END
                ) as por_cobrar,

                SUM(
                    CASE 
                        WHEN payment_methods.name = 'Contado' THEN COALESCE(total,0)
                        WHEN payment_methods.name NOT IN ('Contado','Por Cobrar','Crédito','Prepago') THEN COALESCE(sender_total,0)
                        ELSE 0
                    END
                ) as contado,

                SUM(
                    CASE 
                        WHEN payment_methods.name = 'Crédito' THEN COALESCE(total,0)
                        ELSE 0
                    END
                ) as credito,

                SUM(
                    CASE 
                        WHEN payment_methods.name = 'Prepago' THEN COALESCE(total,0)
                        ELSE 0
                    END
                ) as prepago
            ");

            $this->data = $query->first();
        } elseif ($this->localidad === 'Guatemala') {
            $query = DB::table('shipment_entries')
                ->leftJoin('towns', 'shipment_entries.town_id', '=', 'towns.id')
                ->leftJoin('payment_methods', 'shipment_entries.payment_method_id', '=', 'payment_methods.id')
                ->whereBetween('shipment_entries.date_guide', [$fromFormatted, $toFormatted])
                ->where('prefix_origin', 'CAP')
                ->selectRaw("
                SUM(
                    CASE 
                        WHEN payment_methods.name = 'Por Cobrar' THEN COALESCE(total,0)
                        WHEN payment_methods.name NOT IN ('Contado','Por Cobrar','Crédito','Prepago') THEN COALESCE(receiver_total,0)
                        ELSE 0
                    END
                ) as por_cobrar,

                SUM(
                    CASE 
                        WHEN payment_methods.name = 'Contado' THEN COALESCE(total,0)
                        WHEN payment_methods.name NOT IN ('Contado','Por Cobrar','Crédito','Prepago') THEN COALESCE(sender_total,0)
                        ELSE 0
                    END
                ) as contado,

                SUM(
                    CASE 
                        WHEN payment_methods.name = 'Crédito' THEN COALESCE(total,0)
                        ELSE 0
                    END
                ) as credito,

                SUM(
                    CASE 
                        WHEN payment_methods.name = 'Prepago' THEN COALESCE(total,0)
                        ELSE 0
                    END
                ) as prepago
            ");

            $this->data = $query->first();
        } elseif ($this->localidad === 'Departamental') {
            $query = DB::table('shipment_entries')
                ->leftJoin('towns', 'shipment_entries.town_id', '=', 'towns.id')
                ->leftJoin('payment_methods', 'shipment_entries.payment_method_id', '=', 'payment_methods.id')
                ->whereBetween('shipment_entries.date_guide', [$fromFormatted, $toFormatted])
                ->where('prefix_origin', '!=', 'CAP')
                ->selectRaw("
                SUM(
                    CASE 
                        WHEN payment_methods.name = 'Por Cobrar' THEN COALESCE(total,0)
                        WHEN payment_methods.name NOT IN ('Contado','Por Cobrar','Crédito','Prepago') THEN COALESCE(receiver_total,0)
                        ELSE 0
                    END
                ) as por_cobrar,

                SUM(
                    CASE 
                        WHEN payment_methods.name = 'Contado' THEN COALESCE(total,0)
                        WHEN payment_methods.name NOT IN ('Contado','Por Cobrar','Crédito','Prepago') THEN COALESCE(sender_total,0)
                        ELSE 0
                    END
                ) as contado,

                SUM(
                    CASE 
                        WHEN payment_methods.name = 'Crédito' THEN COALESCE(total,0)
                        ELSE 0
                    END
                ) as credito,

                SUM(
                    CASE 
                        WHEN payment_methods.name = 'Prepago' THEN COALESCE(total,0)
                        ELSE 0
                    END
                ) as prepago
            ");

            $this->data = $query->first();
        }

        $this->dispatch('open-modal', id: 'reporte-modal');
    }

    public function exportarExcel()
    {
        if (!$this->from || !$this->to) {
            Notification::make()
                ->title('Error')
                ->body('Por favor, selecciona un rango de fechas válido.')
                ->danger()
                ->send();
            return;
        }

        $fromFormatted = date('Y-m-d H:i:s', strtotime($this->from));
        $toFormatted = date('Y-m-d H:i:s', strtotime($this->to));

        if ($this->localidad === 'Todo') {
            $data = $this->getAllData();
        } elseif ($this->localidad === 'Guatemala') {
            $data = DB::table('shipment_entries')
                ->leftJoin('towns', 'shipment_entries.town_id', '=', 'towns.id')
                ->leftJoin('payment_methods', 'shipment_entries.payment_method_id', '=', 'payment_methods.id')
                ->leftJoin('shipment_manifests', 'shipment_entries.shipment_manifest_id', '=', 'shipment_manifests.id')
                ->leftJoin('users', 'shipment_entries.created_by', '=', 'users.id')
                ->whereBetween('shipment_entries.date_guide', [$fromFormatted, $toFormatted])
                ->where('prefix_origin', 'CAP')
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
                )
                ->get();
        } elseif ($this->localidad === 'Departamental') {
            $data = DB::table('shipment_entries')
                ->leftJoin('towns', 'shipment_entries.town_id', '=', 'towns.id')
                ->leftJoin('payment_methods', 'shipment_entries.payment_method_id', '=', 'payment_methods.id')
                ->leftJoin('shipment_manifests', 'shipment_entries.shipment_manifest_id', '=', 'shipment_manifests.id')
                ->leftJoin('users', 'shipment_entries.created_by', '=', 'users.id')
                ->whereBetween('shipment_entries.date_guide', [$fromFormatted, $toFormatted])
                ->where('prefix_origin', '!=', 'CAP')
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
                )
                ->get();
        } else {
            $data = collect();
        }

        Logger($data);

        return Excel::download(
            new ShipmentEntriesExport($data),
            'reporte_envios.xlsx'
        );
    }
}
