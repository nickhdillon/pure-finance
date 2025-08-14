<?php

declare(strict_types=1);

namespace App\Livewire;

use Flux\Flux;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\SavingsGoal;
use Livewire\Attributes\Validate;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;

class SavingsGoalForm extends Component
{
    public ?SavingsGoal $savings_goal = null;

    #[Validate(['required', 'int'])]
    public int $account_id;

    #[Validate(['required', 'string'])]
    public string $name = '';

    #[Validate(['required', 'decimal:0,2', 'numeric', 'min:1'])]
    public float $goal_amount;

    #[Validate(['nullable', 'decimal:0,2', 'numeric', 'min:1'])]
    public float $amount_saved;

    #[Validate(['nullable', 'decimal:0,2', 'numeric', 'min:1'])]
    public float $monthly_contribution;

    #[Validate(['boolean'])]
    public bool $target = false;

    #[Validate(['nullable', 'required_if:target,true', 'string'])]
    public string $target_month = '';

    #[Validate(['nullable', 'required_if:target,true', 'string'])]
    public string $target_year = '';

    public Collection $accounts;

    public array $months = [];

    public array $years = [];

    protected function messages(): array
    {
        return [
            'account.required' => 'The saved in account field is required.',
        ];
    }

    public function mount(): void
    {
        $this->months = collect(range(1, 12))->map(
            fn(int $month): string =>
            Carbon::create()
                ->month($month)
                ->format('M')
        )->toArray();

        $this->years = collect(range(Carbon::now()->year, Carbon::now()->year + 20))->toArray();

        $this->getAccounts();

        if ($this->savings_goal) {
            $this->account_id = $this->savings_goal->account_id;
            $this->name = $this->savings_goal->name;
            $this->goal_amount = $this->savings_goal->goal_amount;
            $this->amount_saved = $this->savings_goal->amount_saved;
            $this->monthly_contribution = $this->savings_goal->monthly_contribution;
            $this->target = $this->savings_goal->target;
            $this->target_month = $this->savings_goal->target_month;
            $this->target_year = $this->savings_goal->target_year;
        }
    }

    public function getAccounts(): self
    {
        $this->accounts = auth()
            ->user()
            ->accounts()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        return $this;
    }

    public function submit(): void
    {
        $validated_data = $this->validate();

        if ($this->savings_goal) {
            SavingsGoal::query()
                ->where('id', $this->savings_goal->id)
                ->update($validated_data);
        } else {
            auth()->user()->savings_goals()->create($validated_data);
        }

        $this->dispatch('savings-goal-saved');

        Flux::toast(
            variant: 'success',
            text: 'Savings goal successfully ' . ($this->savings_goal ? 'updated' : 'created'),
        );

        $this->redirectRoute('savings-goals', navigate: true);
    }

    public function delete(): void
    {
        $this->savings_goal?->delete();

        Flux::toast(
            variant: 'success',
            text: 'Savings goal successfully deleted',
        );

        $this->redirectRoute('savings-goals', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.savings-goal-form');
    }
}
