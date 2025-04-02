<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use App\Models\Account;
use Livewire\WithPagination;
use App\Enums\TransactionType;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class AccountOverview extends Component
{
    use WithPagination;

    public Account $account;

    public float $cleared_balance;

    public function render(): View
    {
        $this->account
            ->loadSum(['transactions as cleared_deposits' => function (Builder $query): void {
                $query->whereIn('type', [TransactionType::CREDIT, TransactionType::DEPOSIT])
                    ->where('status', true);
            }], 'amount')
            ->loadSum(['transactions as cleared_debits' => function (Builder $query): void {
                $query->whereIn('type', [TransactionType::DEBIT, TransactionType::TRANSFER, TransactionType::WITHDRAWAL])
                    ->where('status', true);
            }], 'amount');

        $this->account->cleared_balance = $this->account->initial_balance +
            ($this->account->cleared_deposits ?? 0) - ($this->account->cleared_debits ?? 0);

        return view('livewire.account-overview');
    }
}
