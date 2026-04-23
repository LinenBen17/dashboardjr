<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ShipmentEntriesExport implements FromCollection, WithHeadings
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

                'Código Destinatario' => $item->receiver_code,
                'Nombre Destinatario' => $item->receiver_name,
                'Dirección Destinatario' => $item->receiver_address,
                'Teléfono Destinatario' => $item->receiver_phone,

                'Origen' => $item->prefix_origin,
                'Destino' => $item->prefix_destination,

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

            'Código Destinatario',
            'Nombre Destinatario',
            'Dirección Destinatario',
            'Teléfono Destinatario',

            'Origen',
            'Destino',

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
}
