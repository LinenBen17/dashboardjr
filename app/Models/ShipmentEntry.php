<?php

namespace App\Models;

use App\AuditableGuide;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentEntry extends Model
{
    use HasFactory;
    use AuditableGuide;

    protected $fillable = [
        'mother',
        'mother_guide_id',
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
        'town_id',
        'product_id',
        'product_description',
        'pieces',
        'unit_price',
        'sender_total',
        'receiver_total',
        'total',
        'date_guide',
        'payment_method_id',
        'shipment_manifest_id',
        'created_by',
    ];

    public function towns()
    {
        return $this->belongsTo(Town::class, 'town_id');
    }
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function departament()
    {
        return $this->belongsTo(Departament::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function sender()
    {
        return $this->belongsTo(Customer::class, 'sender_code');
    }
    public function receiver()
    {
        return $this->belongsTo(Customer::class, 'receiver_code');
    }

    public function updateFields(array $data): void
    {
        $before = $this->auditData();

        $this->fill($data);
        $this->save();

        [$old, $new] = $this->detectDiff($before, $this->auditData());

        $this->logDiff(
            'updated',
            'Se modificaron datos de la guía',
            $old,
            $new
        );
    }

    public function markAsPrinted(): void
    {
        $this->fecha_impresion = now();
        $this->estado = 'printed';
        $this->save();

        $this->logSnapshot(
            'printed',
            'Se imprimió la guía'
        );
    }
}
