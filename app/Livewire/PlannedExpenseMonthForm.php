<?php

declare(strict_types=1);

namespace App\Livewire;

use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use App\Enums\PlannedExpenseType;
use Illuminate\Contracts\View\View;
use App\Models\PlannedExpenseMonth;

class PlannedExpenseMonthForm extends Component
{
    public PlannedExpenseMonth $expense_month;

    #[Validate(['required', 'string'])]
    public string $name = '';

    #[Validate(['required', 'int'])]
    public int $category_id;

    #[Validate(['required', 'decimal:0,2', 'numeric', 'min:1'])]
    public float $amount;

    public PlannedExpenseType $type = PlannedExpenseType::RECURRING;

    #[Validate(['required', 'bool'])]
    public bool $apply_to_future_months = false;

    public array $categories = [];

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(PlannedExpenseType::class)],
        ];
    }

    public function mount(): void
    {
        $this->name = $this->expense_month->plannedExpense->name;
        $this->category_id = $this->expense_month->plannedExpense->category_id;
        $this->amount = (float) $this->expense_month->amount;
        $this->type = $this->expense_month->plannedExpense->type;

        $this->getCategories();
    }

    #[On('category-saved'), On('planned-expense-saved')]
    public function getCategories(): self
    {
        $this->categories = auth()
            ->user()
            ->categories()
            ->with('children')
            ->select(['id', 'name', 'parent_id'])
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get()
            ->toArray();

        return $this;
    }

    public function submit(): void
    {
        $this->validate();

        $planned_expense = $this->expense_month->plannedExpense;

        $planned_expense->update([
            'name' => $this->name,
            'category_id' => $this->category_id,
            'monthly_amount' => $this->apply_to_future_months
                ? $this->amount
                : $planned_expense->monthly_amount,
            'type' => $this->type,
            'ends_on' => $this->type === PlannedExpenseType::ONE_TIME
                ? $this->expense_month->month
                : null
        ]);

        $this->expense_month->update(['amount' => $this->amount]);

        $this->dispatch('planned-expense-saved');

        Flux::toast(
            variant: 'success',
            text: 'Expense successfully updated',
        );

        Flux::modals()->close();
    }

    public function delete(): void
    {
        $this->expense_month->delete();

        Flux::toast(
            variant: 'success',
            text: 'Expense successfully deleted',
        );

        $this->redirectRoute('planned-spending', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.planned-expense-month-form');
    }
}
