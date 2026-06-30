<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\PlannedExpense;
use Illuminate\Console\Command;
use App\Enums\PlannedExpenseType;
use App\Models\PlannedExpenseMonth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Console\Attributes\Description;

#[Signature('create-recurring-expense-records')]
#[Description('Create recurring planned expense records')]
class CreateRecurringExpenseRecords extends Command
{
    public function handle(): void
    {
        $month = now()->startOfMonth();

        PlannedExpense::query()
            ->where('type', PlannedExpenseType::RECURRING)
            ->where(function (Builder $query) use ($month): void {
                $query->whereNull('ends_on')->orWhereDate('ends_on', '>=', $month);
            })
            ->chunkById(100, function (Collection $planned_expenses) use ($month): void {
                foreach ($planned_expenses as $planned_expense) {
                    PlannedExpenseMonth::firstOrCreate([
                        'planned_expense_id' => $planned_expense->id,
                        'month' => $month->toDateString(),
                        'amount' => $planned_expense->monthly_amount
                    ]);
                }
            });
    }
}
