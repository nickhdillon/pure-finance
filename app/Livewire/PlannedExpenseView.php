<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use App\Enums\TransactionType;
use App\Models\PlannedExpense;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class PlannedExpenseView extends Component
{
    public PlannedExpense $expense;

    public string $timezone = 'America/Chicago';

    public float $total_spent = 0;

    public int $transaction_count = 0;

    public float $available = 0;

    public float $percentage_spent = 0;

    public Collection $monthly_totals;

    public function mount(): void
    {
        $this->monthly_totals = collect();
    }

    private function applyCategoryFilter(Builder $query): void
    {
        $query->where('category_id', $this->expense->category_id)
            ->orWhereRelation('category', 'parent_id', $this->expense->category_id);
    }

    private function getTransactionData(string $start, string $end): Transaction
    {
        return Transaction::query()
            ->whereBetween('date', [$start, $end])
            ->where(fn(Builder $query) => $this->applyCategoryFilter($query))
            ->selectRaw("
                SUM(CASE WHEN type IN (?, ?) THEN amount ELSE 0 END) as total_deposits,
                SUM(CASE WHEN type IN (?, ?, ?) THEN amount ELSE 0 END) as total_debits,
                COUNT(*) as transaction_count
            ", [
                TransactionType::CREDIT,
                TransactionType::DEPOSIT,
                TransactionType::DEBIT,
                TransactionType::TRANSFER,
                TransactionType::WITHDRAWAL
            ])
            ->first();
    }

    private function getCurrentMonthData(): void
    {
        $data = $this->getTransactionData(
            start: now()->timezone($this->timezone)->startOfMonth()->toDateString(),
            end: now()->timezone($this->timezone)->endOfMonth()->toDateString()
        );

        $this->total_spent = abs(($data->total_deposits ?? 0) - ($data->total_debits ?? 0));

        $this->transaction_count = $data->transaction_count;

        $this->available = $this->expense->monthly_amount - $this->total_spent;

        $this->percentage_spent = ($this->total_spent / $this->expense->monthly_amount) * 100;
    }

    private function getTotalSpentLastSixMonths(): void
    {
        $start_of_oldest_month = now()->timezone($this->timezone)->subMonths(6)->startOfMonth()->toDateString();

        $transactions = Transaction::query()
            ->where('date', '>=', $start_of_oldest_month)
            ->where(fn(Builder $query) => $this->applyCategoryFilter($query))
            ->selectRaw("
                SUBSTR(date, 1, 7) as month,
                SUM(CASE WHEN type IN (?, ?) THEN amount ELSE 0 END) as total_deposits,
                SUM(CASE WHEN type IN (?, ?, ?) THEN amount ELSE 0 END) as total_debits
            ", [
                TransactionType::CREDIT,
                TransactionType::DEPOSIT,
                TransactionType::DEBIT,
                TransactionType::TRANSFER,
                TransactionType::WITHDRAWAL
            ])
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get()
            ->keyBy('month');

        $this->monthly_totals = collect(range(1, 6))->mapWithKeys(function (int $i) use ($transactions): array {
            $month = now()->subMonths($i)->format('Y-m');

            $data = $transactions[$month] ?? (object) ['total_deposits' => 0, 'total_debits' => 0];

            return [$month => [
                'month' => now()->subMonths($i)->format('M'),
                'total_spent' => ceil(abs(($data->total_deposits ?? 0) - ($data->total_debits ?? 0))),
            ]];
        });
    }

    public function render(): View
    {
        $this->getCurrentMonthData();

        $this->getTotalSpentLastSixMonths();

        return view('livewire.planned-expense-view');
    }
}
