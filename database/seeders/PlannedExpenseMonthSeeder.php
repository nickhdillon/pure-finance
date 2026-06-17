<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\PlannedExpense;
use Illuminate\Database\Seeder;
use App\Models\PlannedExpenseMonth;

class PlannedExpenseMonthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect(PlannedExpense::get())->each(function (PlannedExpense $planned_expense): void {
            PlannedExpenseMonth::factory()->forPlannedExpense($planned_expense)->create();
        });
    }
}
