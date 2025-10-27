<?php

declare(strict_types=1);

namespace App\Livewire;

use Flux\Flux;
use Livewire\Component;
use App\Models\SavingsGoal;
use App\Enums\TransactionType;
use Livewire\Attributes\Validate;
use Illuminate\Contracts\View\View;

class ContributeWithdrawForm extends Component
{
    public SavingsGoal $savings_goal;

    #[Validate(['nullable', 'decimal:0,2', 'numeric', 'min:1'])]
    public ?float $contribution_amount = null;

    #[Validate(['nullable', 'decimal:0,2', 'numeric', 'min:1'])]
    public ?float $withdrawal_amount = null;

    #[Validate(['bool'])]
    public bool $deduct_from_account = false;

    #[Validate(['bool'])]
    public bool $add_to_account = false;

    public function submit(): void
    {
        $validated_data = $this->validate();

        $this->savings_goal->transactions()->create([
            'account_id' => $this->savings_goal->account_id,
            ...$validated_data
        ]);

        if ($this->deduct_from_account || $this->add_to_account) {
            $category = auth()
                ->user()
                ->categories()
                ->firstOrCreate([
                    'name' => $this->deduct_from_account ? 'Goal Contribution' : 'Goal Withdrawal'
                ]);

            $type = $this->deduct_from_account ? TransactionType::DEBIT : TransactionType::CREDIT;

            $amount = $this->deduct_from_account ? $this->contribution_amount : $this->withdrawal_amount;

            $this->savings_goal->account->transactions()->create([
                'payee' => "{$this->savings_goal->name} Goal",
                'type' => $type,
                'amount' => $amount,
                'category_id' => $category->id,
                'date' => now('America/Chicago'),
                'tags' => null,
                'notes' => null,
                'status' => 0,
                'is_recurring' => false,
            ]);
        }

        $this->dispatch('savings-goal-saved');

        Flux::toast(
            variant: 'success',
            text: 'Savings goal successfully updated',
        );

        Flux::modals()->close();

        $this->reset([
            'contribution_amount',
            'withdrawal_amount',
            'deduct_from_account',
            'add_to_account'
        ]);
    }

    public function render(): View
    {
        return view('livewire.contribute-withdraw-form');
    }
}
