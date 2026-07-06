<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Transaction;
use App\Models\PlannedExpense;
use Livewire\Attributes\Computed;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class PlannedSpending extends Component
{
    public string $sort_by = 'planned_amount';

    public string $sort_direction = 'desc';

    public function sortBy(string $field, string $sort_direction = 'asc'): void
    {
        $this->sort_by = $field;
        $this->sort_direction = $sort_direction;
    }

    #[Computed]
    public function sortLabel(): string
    {
        return match ("{$this->sort_by}:{$this->sort_direction}") {
            'planned_amount:desc' => 'Amount Desc',
            'planned_amount:asc' => 'Amount Asc',
            'name:asc' => 'A-Z',
            'name:desc' => 'Z-A',
            default => 'Amount Desc'
        };
    }

    #[On('planned-expense-saved')]
    public function render(): View
    {
        $timezone = 'America/Chicago';

        $start_of_month = now()->timezone($timezone)->startOfMonth()->toDateString();

        $end_of_month = now()->timezone($timezone)->endOfMonth()->toDateString();

        $expenses = auth()
            ->user()
            ->planned_expenses()
            ->with('currentMonth')
            ->whereDate('starts_on', '<=', $end_of_month)
            ->where(function (Builder $query) use ($start_of_month): void {
                $query
                    ->whereNull('ends_on')
                    ->orWhereDate('ends_on', '>=', $start_of_month);
            })
            ->get();

        $totals = Transaction::query()
            ->selectRaw('
                transactions.category_id,
                categories.parent_id,
                SUM(
                    CASE 
                        WHEN transactions.type IN ("credit", "deposit") THEN -transactions.amount
                        ELSE transactions.amount 
                    END
                ) as total_spent
            ')
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where(function (Builder $query) use ($expenses): void {
                $expense_category_ids = $expenses->pluck('category_id');

                $query->whereIn('transactions.category_id', $expense_category_ids)
                    ->orWhereIn('categories.parent_id', $expense_category_ids);
            })
            ->whereBetween('transactions.date', [$start_of_month, $end_of_month])
            ->groupBy('transactions.category_id', 'categories.parent_id')
            ->get();

        $expenses->each(function (PlannedExpense $expense) use ($totals): void {
            $direct_total = $totals->where('category_id', $expense->category_id)->sum('total_spent');

            $child_total = $totals->where('parent_id', $expense->category_id)->sum('total_spent');

            $expense->total_spent = max(0, $direct_total + $child_total);

            $expense->planned_amount = $expense->currentMonth?->amount ?? $expense->monthly_amount;

            $expense->percentage_spent = ($expense->total_spent / $expense->planned_amount) * 100;
        });
        
        $expenses = $expenses
            ->sortBy(
                fn (PlannedExpense $expense): string|float => match ($this->sort_by) {
                    'name' => $expense->name,
                    'planned_amount' => $expense->planned_amount,
                    default => $expense->planned_amount
                },
                options: SORT_REGULAR,
                descending: $this->sort_direction === 'desc',
            )
            ->values();

        return view('livewire.planned-spending', [
            'expenses' => $expenses,
            'total_spent' => $expenses->sum('total_spent'),
            'total_planned' => $expenses->sum('planned_amount')
        ]);
    }
}
