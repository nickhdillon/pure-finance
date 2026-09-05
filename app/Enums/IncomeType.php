<?php

declare(strict_types=1);

namespace App\Enums;

enum IncomeType: string
{
    case EXPECTED = 'expected';
    case UNPLANNED = 'unplanned';

    public function label(): string
    {
        return match ($this) {
            self::EXPECTED => 'Expected',
            self::UNPLANNED => 'Unplanned',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::EXPECTED => 'emerald',
            self::UNPLANNED => 'amber',
        };
    }
}
