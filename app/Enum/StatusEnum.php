<?php

declare(strict_types=1);

namespace App\Enums;

    enum StatusEnum: string
    {
        case PENDING = 'pending';
        case PAID = 'paid';
        case OVERDUE = 'overdue';
    }
    /* 
    enum StatusInstallmentsEnum: string
    {
        case PENDING = 'pending';
        case PAID = 'paid';
        case OVERDUE = 'overdue';

        public function getColor(): ?string
        {
            return match ($this) {
                self::PENDING => 'info',
                self::PAID => 'success',
                self::OVERDUE => 'error',
            };
        }
    } */