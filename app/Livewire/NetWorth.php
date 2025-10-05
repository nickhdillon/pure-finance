<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use App\Enums\AccountType;
use Livewire\Attributes\Computed;
use Illuminate\Contracts\View\View;

class NetWorth extends Component
{
    #[Computed]
    public function bankingTotal(): float
    {
        return auth()
            ->user()
            ->accounts()
            ->whereIn('type', [AccountType::CHECKING, AccountType::SAVINGS])
            ->sum('balance');
    }

    #[Computed]
    public function debtTotal(): float
    {
        return auth()
            ->user()
            ->accounts()
            ->whereIn('type', [AccountType::CREDIT_CARD, AccountType::LOAN])
            ->sum('balance');
    }

    #[Computed]
    public function investmentTotal(): float
    {
        return auth()
            ->user()
            ->accounts()
            ->where('type', AccountType::INVESTMENT)
            ->sum('balance');
    }

    public function render(): View
    {
        return view('livewire.net-worth');
    }
}
