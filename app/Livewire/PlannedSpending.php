<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Transaction;
use App\Models\PlannedExpense;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class PlannedSpending extends Component
{
    #[On('planned-expense-saved')]
    public function render(): View
    {
        $expenses = auth()->user()->planned_expenses;

        $timezone = 'America/Chicago';

        $start_of_month = now()->timezone($timezone)->startOfMonth()->toDateString();

        $end_of_month = now()->timezone($timezone)->endOfMonth()->toDateString();

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
        });

        return view('livewire.planned-spending', ['expenses' => $expenses]);
    }
}
