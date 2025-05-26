<?php

declare(strict_types=1);

namespace App\Livewire;

use Flux\Flux;
use Livewire\Component;
use App\Models\SavingsGoal;
use Livewire\Attributes\Validate;
use Illuminate\Contracts\View\View;

class ContributeWithdrawForm extends Component
{
    public SavingsGoal $savings_goal;

    #[Validate(['nullable', 'decimal:0,2', 'numeric', 'min:1'])]
    public ?float $contribution_amount = null;

    #[Validate(['nullable', 'decimal:0,2', 'numeric', 'min:1'])]
    public ?float $withdrawal_amount = null;

    public function submit(): void
    {
        $validated_data = $this->validate();

        $this->savings_goal->transactions()->create([
            'account_id' => $this->savings_goal->account_id,
            ...$validated_data
        ]);

        $this->dispatch('savings-goal-saved');

        Flux::toast(
            variant: 'success',
            text: 'Savings goal successfully updated',
        );

        Flux::modals()->close();

        $this->reset(['contribution_amount', 'withdrawal_amount']);
    }

    public function render(): View
    {
        return view('livewire.contribute-withdraw-form');
    }
}
