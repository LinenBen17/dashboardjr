<?php

namespace App\Filament\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

use App\Exports\ShipmentEntriesExport;
use App\Exports\ShipmentManifestsExport;
use App\Filament\Clusters\Shipping;
use Maatwebsite\Excel\Facades\Excel;

class ShipmentReports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.shipment-reports';

    protected static ?string $cluster = Shipping::class;

    protected static ?string $navigationLabel = 'Reporte de Envíos';
    protected static ?string $modelLabel = 'Reporte de Envíos';

    public $from;
    public $to;
    public $cliente;
    // public $agrupacion = 'destino';
    public $tipo = 'detallado';
    public $localidad = 'Todo';
    // public $contraEntrega = false;

    public $manifest_auditable = '';

    public $tipoReporte = 'guias';

    public $data;

    public $totalRegistros = 0;

    public function getAllData()
    {
        $fromFormatted = date('Y-m-d H:i:s', strtotime($this->from));
        $toFormatted = date('Y-m-d H:i:s', strtotime($this->to));

        $query = DB::table('shipment_entries')
            ->leftJoin('towns', 'shipment_entries.town_id', '=', 'towns.id')
            ->leftJoin('customers as sender', 'shipment_entries.sender_code', '=', 'sender.id')
            ->leftJoin('customers as receiver', 'shipment_entries.receiver_code', '=', 'receiver.id')
            ->leftJoin('payment_methods', 'shipment_entries.payment_method_id', '=', 'payment_methods.id')
            ->leftJoin('shipment_manifests', 'shipment_entries.shipment_manifest_id', '=', 'shipment_manifests.id')
            ->leftJoin('shipment_deliveries', 'shipment_entries.id', '=', 'shipment_deliveries.shipment_entry_id')
            ->leftJoin('users', 'shipment_entries.created_by', '=', 'users.id')
            ->whereBetween('shipment_entries.date_guide', [$fromFormatted, $toFormatted])
            ->select(
                'shipment_entries.id',
                'mother',
                'sender.code as sender_code',
                'sender_name',
                'sender_address',
                'sender_phone',
                'receiver.code as receiver_code',
                'receiver_name',
                'receiver_address',
                'receiver_phone',
                'shipment_deliveries.received_by_name as received_by',
                'shipment_deliveries.received_by_document as received_by_document',
                'shipment_deliveries.signed as signed',
                'shipment_deliveries.observations as delivery_observations',
                'shipment_entries.prefix_origin as prefix_origin',
                'shipment_entries.prefix_destination as     prefix_destination',
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

            $this->totalRegistros = DB::table('shipment_entries')
                ->whereBetween('date_guide', [$fromFormatted, $toFormatted])
                ->count();

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

            $this->totalRegistros = DB::table('shipment_entries')
                ->whereBetween('date_guide', [$fromFormatted, $toFormatted])
                ->where('prefix_origin', 'CAP')
                ->count();

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

            $this->totalRegistros = DB::table('shipment_entries')
                ->whereBetween('date_guide', [$fromFormatted, $toFormatted])
                ->where('prefix_origin', '!=', 'CAP')
                ->count();

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

        $this->tipoReporte = 'guias';

        $data = collect();

        $fromFormatted = date('Y-m-d H:i:s', strtotime($this->from));
        $toFormatted = date('Y-m-d H:i:s', strtotime($this->to));

        if ($this->localidad === 'Todo') {
            if (!$this->manifest_auditable) {
                $data = $this->getAllData();
            } else {
                $data = DB::table('shipment_entries')
                    ->leftJoin('payment_methods', 'shipment_entries.payment_method_id', '=', 'payment_methods.id')
                    ->leftJoin('shipment_manifests', 'shipment_entries.shipment_manifest_id', '=', 'shipment_manifests.id')
                    ->whereBetween('shipment_entries.date_guide', [$fromFormatted, $toFormatted])
                    ->selectRaw("
                        shipment_manifests.manifest_code as manifest_code,
                        shipment_manifests.date as manifest_date,
                        shipment_entries.prefix_origin,
                        shipment_entries.prefix_destination,

                        SUM(COALESCE(shipment_entries.total,0)) as total_manifesto,

                        SUM(
                            CASE 
                                WHEN payment_methods.name = 'Contado' THEN COALESCE(shipment_entries.total,0)
                                WHEN payment_methods.name NOT IN ('Contado','Por Cobrar','Crédito','Prepago') THEN COALESCE(shipment_entries.sender_total,0)
                                ELSE 0
                            END
                        ) as contado_total,

                        SUM(
                            CASE 
                                WHEN payment_methods.name = 'Por Cobrar' THEN COALESCE(shipment_entries.total,0)
                                WHEN payment_methods.name NOT IN ('Contado','Por Cobrar','Crédito','Prepago') THEN COALESCE(shipment_entries.receiver_total,0)
                                ELSE 0
                            END
                        ) as por_cobrar_total,

                        SUM(
                            CASE 
                                WHEN payment_methods.name = 'Crédito' THEN COALESCE(shipment_entries.total,0)
                                ELSE 0
                            END
                        ) as credito_total,

                        SUM(
                            CASE 
                                WHEN payment_methods.name = 'Prepago' THEN COALESCE(shipment_entries.total,0)
                                ELSE 0
                            END
                        ) as prepago_total
                    ")
                    ->groupBy(
                        'shipment_manifests.manifest_code',
                        'shipment_manifests.date',
                        'shipment_entries.prefix_origin',
                        'shipment_entries.prefix_destination'
                    )
                    ->get();

                $this->tipoReporte = 'manifiestos';
            }
        } elseif ($this->localidad === 'Guatemala') {
            if (!$this->manifest_auditable) {
                $data = DB::table('shipment_entries')
                    ->leftJoin('towns', 'shipment_entries.town_id', '=', 'towns.id')
                    ->leftJoin('customers as sender', 'shipment_entries.sender_code', '=', 'sender.id')
                    ->leftJoin('customers as receiver', 'shipment_entries.receiver_code', '=', 'receiver.id')
                    ->leftJoin('payment_methods', 'shipment_entries.payment_method_id', '=', 'payment_methods.id')
                    ->leftJoin('shipment_manifests', 'shipment_entries.shipment_manifest_id', '=', 'shipment_manifests.id')
                    ->leftJoin('users', 'shipment_entries.created_by', '=', 'users.id')
                    ->whereBetween('shipment_entries.date_guide', [$fromFormatted, $toFormatted])
                    ->where('prefix_origin', 'CAP')
                    ->select(
                        'shipment_entries.id',
                        'mother',
                        'sender.code as sender_code',
                        'sender_name',
                        'sender_address',
                        'sender_phone',
                        'receiver.code as receiver_code',
                        'receiver_name',
                        'receiver_address',
                        'receiver_phone',
                        'shipment_entries.prefix_origin as prefix_origin',
                        'shipment_entries.prefix_destination as prefix_destination',
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
                $data = DB::table('shipment_entries')
                    ->leftJoin('payment_methods', 'shipment_entries.payment_method_id', '=', 'payment_methods.id')
                    ->leftJoin('shipment_manifests', 'shipment_entries.shipment_manifest_id', '=', 'shipment_manifests.id')
                    ->leftJoin('shipment_deliveries', 'shipment_entries.id', '=', 'shipment_deliveries.shipment_entry_id')
                    ->whereBetween('shipment_entries.date_guide', [$fromFormatted, $toFormatted])
                    ->where('prefix_origin', 'CAP')
                    ->selectRaw("
                        shipment_manifests.manifest_code as manifest_code,
                        shipment_manifests.date as manifest_date,
                        shipment_entries.prefix_origin,
                        shipment_entries.prefix_destination,

                        SUM(COALESCE(shipment_entries.total,0)) as total_manifesto,

                        SUM(
                            CASE 
                                WHEN payment_methods.name = 'Contado' THEN COALESCE(shipment_entries.total,0)
                                WHEN payment_methods.name NOT IN ('Contado','Por Cobrar','Crédito','Prepago') THEN COALESCE(shipment_entries.sender_total,0)
                                ELSE 0
                            END
                        ) as contado_total,

                        SUM(
                            CASE 
                                WHEN payment_methods.name = 'Por Cobrar' THEN COALESCE(shipment_entries.total,0)
                                WHEN payment_methods.name NOT IN ('Contado','Por Cobrar','Crédito','Prepago') THEN COALESCE(shipment_entries.receiver_total,0)
                                ELSE 0
                            END
                        ) as por_cobrar_total,

                        SUM(
                            CASE 
                                WHEN payment_methods.name = 'Crédito' THEN COALESCE(shipment_entries.total,0)
                                ELSE 0
                            END
                        ) as credito_total,

                        SUM(
                            CASE 
                                WHEN payment_methods.name = 'Prepago' THEN COALESCE(shipment_entries.total,0)
                                ELSE 0
                            END
                        ) as prepago_total
                    ")
                    ->groupBy(
                        'shipment_manifests.manifest_code',
                        'shipment_manifests.date',
                        'shipment_entries.prefix_origin',
                        'shipment_entries.prefix_destination'
                    )
                    ->get();
                $this->tipoReporte = 'manifiestos';
            }
        } elseif ($this->localidad === 'Departamental') {
            if (!$this->manifest_auditable) {
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
                $data = DB::table('shipment_entries')
                    ->leftJoin('payment_methods', 'shipment_entries.payment_method_id', '=', 'payment_methods.id')
                    ->leftJoin('shipment_manifests', 'shipment_entries.shipment_manifest_id', '=', 'shipment_manifests.id')
                    ->leftJoin('shipment_deliveries', 'shipment_entries.id', '=', 'shipment_deliveries.shipment_entry_id')
                    ->whereBetween('shipment_entries.date_guide', [$fromFormatted, $toFormatted])
                    ->where('prefix_origin', '!=', 'CAP')
                    ->selectRaw("
                        shipment_manifests.manifest_code as manifest_code,
                        shipment_manifests.date as manifest_date,
                        shipment_entries.prefix_origin,
                        shipment_entries.prefix_destination,

                        SUM(COALESCE(shipment_entries.total,0)) as total_manifesto,

                        SUM(
                            CASE 
                                WHEN payment_methods.name = 'Contado' THEN COALESCE(shipment_entries.total,0)
                                WHEN payment_methods.name NOT IN ('Contado','Por Cobrar','Crédito','Prepago') THEN COALESCE(shipment_entries.sender_total,0)
                                ELSE 0
                            END
                        ) as contado_total,

                        SUM(
                            CASE 
                                WHEN payment_methods.name = 'Por Cobrar' THEN COALESCE(shipment_entries.total,0)
                                WHEN payment_methods.name NOT IN ('Contado','Por Cobrar','Crédito','Prepago') THEN COALESCE(shipment_entries.receiver_total,0)
                                ELSE 0
                            END
                        ) as por_cobrar_total,

                        SUM(
                            CASE 
                                WHEN payment_methods.name = 'Crédito' THEN COALESCE(shipment_entries.total,0)
                                ELSE 0
                            END
                        ) as credito_total,

                        SUM(
                            CASE 
                                WHEN payment_methods.name = 'Prepago' THEN COALESCE(shipment_entries.total,0)
                                ELSE 0
                            END
                        ) as prepago_total
                    ")
                    ->groupBy(
                        'shipment_manifests.manifest_code',
                        'shipment_manifests.date',
                        'shipment_entries.prefix_origin',
                        'shipment_entries.prefix_destination'
                    )
                    ->get();
                $this->tipoReporte = 'manifiestos';
            }
        } else {
            $data = collect();
        }

        if ($this->tipoReporte === 'guias') {
            return Excel::download(
                new ShipmentEntriesExport($data),
                'reporte_guias.xlsx'
            );
        }

        if ($this->tipoReporte === 'manifiestos') {
            return Excel::download(
                new ShipmentManifestsExport($data),
                'reporte_manifiestos.xlsx'
            );
        }
    }
}
