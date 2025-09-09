<?php

namespace App;

enum GuideStatus: string
{
    case PENDING = 'pending'; // pendiente de recolección
    case PICKED_UP = 'picked_up'; // recolectada
    case IN_TRANSIT = 'in_transit'; // en tránsito
    case AT_HUB = 'at_hub'; // en centro de distribución
    case OUT_FOR_DELIVERY = 'out_for_delivery'; // en ruta de entrega
    case DELIVERED = 'delivered'; // entregada
    case FAILED_DELIVERY = 'failed_delivery'; // intento fallido de entrega
    case RETURNED = 'returned'; // devuelta al remitente
    case CANCELED = 'canceled'; // cancelada

    // Método auxiliar para obtener todos los estados como array
    public static function all(): array
    {
        return array_map(fn($status) => $status->value, self::cases());
    }
}
