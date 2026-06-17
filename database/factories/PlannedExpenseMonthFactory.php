<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\PlannedExpense;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlannedExpenseMonth>
 */
class PlannedExpenseMonthFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'planned_expense_id' => PlannedExpense::factory(),
            'month' => now()->startOfMonth()->toDateString(),
            'amount' => $this->faker->randomFloat(2, 10, 500)
        ];
    }

    public function forPlannedExpense(PlannedExpense $planned_expense): static
    {
        return $this->state(fn (): array => [
            'planned_expense_id' => $planned_expense->id,
            'month' => $planned_expense->starts_on->format('Y-m-01'),
            'amount' => $planned_expense->monthly_amount,
        ]);
    }
}
