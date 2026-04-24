<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ShipmentEntriesExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function ($item) {
            return [
                'Guía' => $item->mother,

                'Código Remitente' => $item->sender_code,
                'Nombre Remitente' => $item->sender_name,
                'Dirección Remitente' => $item->sender_address,
                'Teléfono Remitente' => $item->sender_phone,

                'Origen' => $item->prefix_origin,
                'Destino' => $item->prefix_destination,

                'Código Destinatario' => $item->receiver_code,
                'Nombre Destinatario' => $item->receiver_name,
                'Dirección Destinatario' => $item->receiver_address,
                'Teléfono Destinatario' => $item->receiver_phone,

                'Nombre Recibido' => $item->received_by,
                'DPI Recibido' => (string) $item->received_by_document,
                'Firmado' => $item->signed ? 'Sí' : 'No',
                'Observaciones Entrega' => $item->delivery_observations,

                'Descripción Producto' => $item->product_description,
                'Piezas' => (float) $item->pieces,

                'Total Remitente' => (float) $item->sender_total,
                'Total Destinatario' => (float) $item->receiver_total,
                'Total' => (float) $item->total,

                'Fecha' => $item->date_guide,

                'Forma de Pago' => $item->payment_method,
                'Manifiesto' => $item->manifest_code,
                'Usuario' => $item->created_by,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Guía',

            'Código Remitente',
            'Nombre Remitente',
            'Dirección Remitente',
            'Teléfono Remitente',

            'Origen',
            'Destino',

            'Código Destinatario',
            'Nombre Destinatario',
            'Dirección Destinatario',
            'Teléfono Destinatario',

            'Nombre Recibido',
            'DPI Recibido',
            'Firmado',
            'Observaciones Entrega',

            'Descripción Producto',
            'Piezas',

            'Total Remitente',
            'Total Destinatario',
            'Total',

            'Fecha',

            'Forma de Pago',
            'Manifiesto',
            'Usuario',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [ // fila 1 = encabezados
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '000000'], // texto color negro
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '00FFEF'], // color turquesa fondo
                ],
            ],
        ];
    }
}
