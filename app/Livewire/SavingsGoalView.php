<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use App\Models\SavingsGoal;
use Livewire\Attributes\On;
use Illuminate\Contracts\View\View;
use App\Models\SavingsGoalTransaction;

class SavingsGoalView extends Component
{
    public SavingsGoal $savings_goal;

    public float $total_saved = 0;

    public float $total_spent = 0;

    public float $percentage_saved = 0;

    public float $left_to_save = 0;

    private function getTransactionData(): SavingsGoalTransaction
    {
        return SavingsGoalTransaction::query()
            ->whereRelation('savings_goal', 'id', $this->savings_goal->id)
            ->selectRaw(
                'SUM(contribution_amount) as contribution_total, 
                SUM(withdrawal_amount) as withdrawal_total'
            )
            ->first();
    }

    private function getSavingsGoalData(): void
    {
        $data = $this->getTransactionData();

        $contributions = $data->contribution_total ?? 0;
        $withdrawals = $data->withdrawal_total ?? 0;

        $this->total_saved = abs(($contributions ?? 0) - ($withdrawals ?? 0)) + $this->savings_goal->amount_saved;

        $this->total_spent = $withdrawals ?? 0;

        $this->percentage_saved = ($this->total_saved / $this->savings_goal->goal_amount) * 100;

        $this->left_to_save = $this->savings_goal->goal_amount - $this->total_saved;
    }

    #[On('savings-goal-saved')]
    public function render(): View
    {
        $this->getSavingsGoalData();

        return view('livewire.savings-goal-view');
    }
}
