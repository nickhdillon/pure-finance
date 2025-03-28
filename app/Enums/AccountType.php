<?php

declare(strict_types=1);

namespace App\Enums;

enum AccountType: string
{
    case CHECKING = 'checking';
    case CREDIT_CARD = 'credit_card';
    case INVESTMENT = 'investment';
    case LOAN = 'loan';
    case SAVINGS = 'savings';

    public function label(): string
    {
        return match ($this) {
            self::CHECKING => 'Checking',
            self::CREDIT_CARD => 'Credit Card',
            self::INVESTMENT => 'Investment',
            self::LOAN => 'Loan',
            self::SAVINGS => 'Savings',
        };
    }
}
