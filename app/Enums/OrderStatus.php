<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case InDelivery = 'in_delivery';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    // Определяем, из какого статуса в какой можно перейти
    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Pending => in_array($target, [self::Paid, self::Cancelled]),
            self::Paid => in_array($target, [self::InDelivery, self::Cancelled]),
            self::InDelivery => in_array($target, [self::Delivered, self::Cancelled]),
            self::Delivered, self::Cancelled => false,
        };
    }
}
