<?php

declare(strict_types=1);

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Transaction;
use Livewire\Attributes\On;
use App\Enums\TransactionType;
use App\Models\PlannedExpense;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class PlannedExpenseView extends Component
{
    public PlannedExpense $expense;

    public string $timezone = 'America/Chicago';

    public float $total_spent = 0;

    public int $transaction_count = 0;

    public float $available = 0;

    public float $percentage_spent = 0;

    public Collection $monthly_totals;

    public EloquentCollection $transactions;

    public string $selected_month = '';

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
        $now = now()->setTimezone($this->timezone);

        $start_of_oldest_month = $now->copy()->startOfMonth()->subMonths(6)->toDateString();

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

        $this->monthly_totals = collect(range(1, 6))->mapWithKeys(function (int $i) use ($transactions, $now): array {
            $date = $now->copy()->startOfMonth()->subMonths($i);

            $month = $date->format('Y-m');

            $data = $transactions[$month] ?? (object) ['total_deposits' => 0, 'total_debits' => 0];

            return [$month => [
                'month' => $date->format('M'),
                'total_spent' => ceil(abs(($data->total_deposits ?? 0) - ($data->total_debits ?? 0))),
            ]];
        });
    }

    #[On('load-transactions')]
    public function loadTransactions(?string $month = null): void
    {
        $this->reset('transactions');

        $timezone = 'America/Chicago';

        $now = now($timezone);

        if ($month) {
            $month_number = Carbon::parse($month)->month;

            $year = $month_number > $now->month ? $now->year - 1 : $now->year;

            $start = Carbon::createFromDate($year, $month_number, 1, $timezone)->startOfMonth();
            $end = (clone $start)->endOfMonth();
        } else {
            $start = $now->startOfMonth();
            $end = (clone $start)->endOfMonth();
        }

        $this->transactions = Transaction::query()
            ->with(['category:id,name,parent_id', 'category.parent:id,name'])
            ->select(['id', 'category_id', 'type', 'amount', 'payee', 'slug', 'date'])
            ->where(fn(Builder $query) => $this->applyCategoryFilter($query))
            ->whereBetween('date', [$start, $end])
            ->latest('date')
            ->get();

        $this->selected_month = (clone $start)->format('F') . " ({$this->transactions->count()})";
    }

    public function resetTransactions(): void
    {
        $this->reset('transactions');
    }

    #[On('planned-expense-saved')]
    public function render(): View
    {
        $this->getCurrentMonthData();

        $this->getTotalSpentLastSixMonths();

        return view('livewire.planned-expense-view');
    }
}
