<?php

namespace App;

use App\Models\GuideLog;
use Filament\Facades\Filament;

trait AuditableGuide
{
    public function auditData(): array
    {
        return $this->only([
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
        ]);
    }

    public function detectDiff(array $before, array $after): array
    {
        $old = [];
        $new = [];

        foreach ($after as $key => $value) {
            if (($before[$key] ?? null) !== $value) {
                $old[$key] = $before[$key] ?? null;
                $new[$key] = $value;
            }
        }

        return [$old, $new];
    }

    public function logSnapshot(string $action, string $description): void
    {
        GuideLog::create([
            'shipment_entry_id' => $this->id,
            'user_id' => Filament::auth()->user()->id,
            'action' => $action,
            'description' => $description,
            'old_data' => null,
            'new_data' => $this->auditData(),
        ]);
    }

    public function logDiff(
        string $action,
        string $description,
        array $old,
        array $new
    ): void {
        if (empty($old)) {
            return;
        }

        GuideLog::create([
            'shipment_entry_id' => $this->id,
            'user_id' => Filament::auth()->user()->id,
            'action' => $action,
            'description' => $description,
            'old_data' => $old,
            'new_data' => $new,
        ]);
    }
}
