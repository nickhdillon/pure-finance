<?php

declare(strict_types=1);

namespace App\Enums;

enum AccountType: string
{
	case CHECKING = 'checking';
	case SAVINGS = 'savings';
	case CREDIT_CARD = 'credit_card';
	case INVESTMENT	= 'investment';
	case LOAN = 'loan';

	public function label(): string
	{
		return match ($this) {
			self::CHECKING => 'Checking',
			self::SAVINGS => 'Savings',
			self::CREDIT_CARD => 'Credit Card',
			self::INVESTMENT => 'Investment',
			self::LOAN => 'Loan',
		};
	}
}
