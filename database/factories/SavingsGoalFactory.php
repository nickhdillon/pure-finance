<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Account;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SavingsGoal>
 */
class SavingsGoalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $goal_name = Arr::random(['House', 'Car']);

        $now = now()->startOfMonth();
        $target_month_number = rand(1, 12);
        $target_year = rand($now->year, $now->year + 5);
        $target_date = Carbon::create($target_year, $target_month_number)->startOfMonth();

        $goal_amount = rand(3000, 5000);
        $amount_saved = rand(500, 2000);
        $amount_remaining = max(0, $goal_amount - $amount_saved);

        $months_remaining = $now->diffInMonths($target_date) + 1; // includes current month
        $monthly_contribution = $months_remaining > 0
            ? round($amount_remaining / $months_remaining, 2)
            : $amount_remaining;

        return [
            'account_id' => Account::first()->id,
            'name' => $goal_name,
            'slug' => Str::slug($goal_name),
            'goal_amount' => $goal_amount,
            'amount_saved' => $amount_saved,
            'monthly_contribution' => $monthly_contribution,
            'last_contributed' => now(),
            'target' => true,
            'target_month' => Carbon::create()->month($target_month_number)->format('M'),
            'target_year' => (string) $target_year,
        ];
    }
}
