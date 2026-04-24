<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ShipmentManifestsExport implements FromCollection, WithHeadings
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
                'Manifiesto' => $item->manifest_code,
                'Fecha' => $item->manifest_date,
                'Origen' => $item->prefix_origin,
                'Destino' => $item->prefix_destination,
                'Total Manifiesto' => (float) $item->total_manifesto,
                'Contado' => (float) $item->contado_total,
                'Por Cobrar' => (float) $item->por_cobrar_total,
                'Crédito' => (float) $item->credito_total,
                'Prepago' => (float) $item->prepago_total,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Manifiesto',
            'Fecha',
            'Origen',
            'Destino',
            'Total Manifiesto',
            'Contado',
            'Por Cobrar',
            'Crédito',
            'Prepago',
        ];
    }
}
