<?php

declare(strict_types=1);

namespace App\Enums;

enum BillAlert: string
{
	case DAY_OF = 'day_of';
	case ONE_DAY_BEFORE = 'one_day_before';
	case TWO_DAYS_BEFORE = 'two_days_before';
	case ONE_WEEK_BEFORE = 'one_week_before';

	public function label(): string
	{
		return match ($this) {
			self::DAY_OF => 'On day of bill',
			self::ONE_DAY_BEFORE => '1 day before',
			self::TWO_DAYS_BEFORE => '2 days before',
			self::ONE_WEEK_BEFORE => '1 week before',
		};
	}
}
