<?php

namespace Database\Seeders;

use App\Models\SavingsGoal;
use Illuminate\Database\Seeder;
use App\Models\SavingsGoalTransaction;

class SavingsGoalTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SavingsGoalTransaction::factory(2)
            ->for(SavingsGoal::first(), 'savings_goal')
            ->sequence(
                ['contribution_amount' => 200, 'withdrawal_amount' => null],
                ['contribution_amount' => null, 'withdrawal_amount' => 100]
            )
            ->create();

        SavingsGoalTransaction::factory(2)
            ->for(SavingsGoal::find(2), 'savings_goal')
            ->sequence(
                ['contribution_amount' => 400, 'withdrawal_amount' => null],
                ['contribution_amount' => null, 'withdrawal_amount' => 200]
            )
            ->create();
    }
}
