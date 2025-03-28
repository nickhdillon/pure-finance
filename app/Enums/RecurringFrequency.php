<?php

declare(strict_types=1);

namespace App\Enums;

enum RecurringFrequency: string
{
    case MONTHLY = 'month';
    case YEARLY = 'year';

    public function label(): string
    {
        return match ($this) {
            self::MONTHLY => 'Month',
            self::YEARLY => 'Year',
        };
    }
}
