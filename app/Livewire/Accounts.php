<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use App\Models\Account;
use Livewire\Attributes\On;
use App\Enums\TransactionType;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\Database\Eloquent\Builder;

#[On('account-saved'), On('status-changed')]
class Accounts extends Component
{
    public function render(): View
    {
        $accounts = auth()
            ->user()
            ->accounts()
            ->withCount('transactions')
            ->withSum(['transactions as cleared_deposits' => function (Builder $query): void {
                $query->whereIn('type', [TransactionType::CREDIT, TransactionType::DEPOSIT])
                    ->where('status', true);
            }], 'amount')
            ->withSum(['transactions as cleared_debits' => function (Builder $query): void {
                $query->whereIn('type', [TransactionType::DEBIT, TransactionType::TRANSFER, TransactionType::WITHDRAWAL])
                    ->where('status', true);
            }], 'amount')
            ->orderBy('name')
            ->get()
            ->map(function (Account $account): Account {
                $account->cleared_balance = $account->initial_balance +
                    ($account->cleared_deposits ?? 0) - ($account->cleared_debits ?? 0);

                return $account;
            });

        return view('livewire.accounts', [
            'accounts' => $accounts,
            'available_total' => $accounts->sum('balance'),
            'cleared_total' => $accounts->sum('initial_balance') +
                ($accounts->sum('cleared_deposits') ?? 0) - ($accounts->sum('cleared_debits') ?? 0),
        ]);
    }
}
