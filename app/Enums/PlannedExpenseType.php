<?php

declare(strict_types=1);

namespace App\Enums;

enum PlannedExpenseType: string
{
	case RECURRING = 'recurring';
	case ONE_TIME = 'one_time';

	public function label(): string
	{
		return match ($this) {
			self::RECURRING => 'Monthly',
			self::ONE_TIME => 'One-Time'
		};
	}

	public function color(): string
	{
		return match ($this) {
			self::RECURRING => 'emerald',
			self::ONE_TIME => 'blue'
		};
	}
}
