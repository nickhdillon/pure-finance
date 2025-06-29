<?php

declare(strict_types=1);

namespace App\Enums;

enum RecurringFrequency: string
{
    case ONE_TIME = 'one_time';
    case MONTHLY = 'month';
    case QUARTERLY = 'quarter';
    case SEMI_ANNUALLY = 'semi_annual';
    case YEARLY = 'year';

    public function label(): string
    {
        return match ($this) {
            self::ONE_TIME => 'One Time',
            self::MONTHLY => 'Month',
            self::QUARTERLY => '3 Months',
            self::SEMI_ANNUALLY => '6 Months',
            self::YEARLY => 'Year',
        };
    }
}
