<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use App\Models\Account;
use App\Enums\AccountType;
use Livewire\Attributes\On;
use App\Enums\TransactionType;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Database\Eloquent\Builder;

#[On('account-saved'), On('status-changed')]
class Accounts extends Component
{
    public function render(): View
    {
        $accounts = auth()->user()
            ->accounts()
            ->withCount('transactions')
            ->withSum([
                // Banking accounts
                'transactions as banking_cleared_deposits' => function (Builder $query): void {
                    $query->whereIn('type', [TransactionType::CREDIT, TransactionType::DEPOSIT])
                        ->whereIn('accounts.type', [AccountType::CHECKING, AccountType::SAVINGS])
                        ->where('status', true);
                },
                'transactions as banking_cleared_debits' => function (Builder $query): void {
                    $query->whereIn('type', [TransactionType::DEBIT, TransactionType::TRANSFER, TransactionType::WITHDRAWAL])
                        ->whereIn('accounts.type', [AccountType::CHECKING, AccountType::SAVINGS])
                        ->where('status', true);
                },

                // Debt accounts
                'transactions as debt_cleared_deposits' => function (Builder $query): void {
                    $query->whereIn('type', [TransactionType::CREDIT, TransactionType::DEPOSIT])
                        ->whereIn('accounts.type', [AccountType::CREDIT_CARD, AccountType::LOAN])
                        ->where('status', true);
                },
                'transactions as debt_cleared_debits' => function (Builder $query): void {
                    $query->whereIn('type', [TransactionType::DEBIT, TransactionType::TRANSFER, TransactionType::WITHDRAWAL])
                        ->whereIn('accounts.type', [AccountType::CREDIT_CARD, AccountType::LOAN])
                        ->where('status', true);
                },

                // Investment accounts
                'transactions as investment_cleared_deposits' => function (Builder $query): void {
                    $query->whereIn('type', [TransactionType::CREDIT, TransactionType::DEPOSIT])
                        ->where('accounts.type', AccountType::INVESTMENT)
                        ->where('status', true);
                },
                'transactions as investment_cleared_debits' => function (Builder $query): void {
                    $query->whereIn('type', [TransactionType::DEBIT, TransactionType::TRANSFER, TransactionType::WITHDRAWAL])
                        ->where('accounts.type', AccountType::INVESTMENT)
                        ->where('status', true);
                },
            ], 'amount')
            ->orderBy('name')
            ->get()
            ->map(function (Account $account): Account {
                if (in_array($account->type, [AccountType::LOAN, AccountType::CREDIT_CARD])) {
                    $account->cleared_balance = $account->initial_balance
                        - ($account->debt_cleared_debits ?? 0)
                        + ($account->debt_cleared_deposits ?? 0);
                } else {
                    $account->cleared_balance = $account->initial_balance
                        + ($account->cleared_deposits ?? 0)
                        - ($account->cleared_debits ?? 0);
                }

                return $account;
            });

        $group_definitions = [
            'banking' => [AccountType::CHECKING, AccountType::SAVINGS],
            'debt' => [AccountType::CREDIT_CARD, AccountType::LOAN],
            'investment' => [AccountType::INVESTMENT],
        ];

        $grouped_accounts = collect($group_definitions)
            ->mapWithKeys(fn(array $types, string $group_name): array => [
                $group_name => $accounts->whereIn('type', $types)
            ])
            ->filter(fn(Collection $group_accounts): bool => $group_accounts->isNotEmpty())
            ->mapWithKeys(fn(Collection $group_accounts, string $group_name): array => [
                $group_name => [
                    'accounts' => $group_accounts,
                    'available_total' => $group_accounts->sum('balance'),
                    'cleared_total' => $group_accounts->sum('cleared_balance'),
                ]
            ]);

        return view('livewire.accounts', [
            'grouped_accounts' => $grouped_accounts,
        ]);
    }
}
