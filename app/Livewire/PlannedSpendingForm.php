<?php

declare(strict_types=1);

namespace App\Livewire;

use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Validation\Rule;
use App\Enums\PlannedExpenseType;
use Livewire\Attributes\Validate;
use Illuminate\Contracts\View\View;

class PlannedSpendingForm extends Component
{
    #[Validate(['required', 'string'])]
    public string $name = '';

    #[Validate(['required', 'int'])]
    public int $category_id;

    #[Validate(['required', 'decimal:0,2', 'numeric', 'min:1'])]
    public float $monthly_amount;

    public PlannedExpenseType $type = PlannedExpenseType::RECURRING;

    public array $categories = [];

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(PlannedExpenseType::class)],
        ];
    }

    protected function messages(): array
    {
        return [
            'category_id.required' => 'The category field is required.',
        ];
    }

    public function mount(): void
    {
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
        $validated_data = $this->validate();

        $month = now('America/Chicago')->startOfMonth()->toDateString();

        $expense = auth()->user()->planned_expenses()->create([
            ...$validated_data,
            'starts_on' => $month,
            'ends_on' => $this->type === PlannedExpenseType::ONE_TIME ? $month : null,
        ]);

        $expense->months()->create([
            'month' => $month,
            'amount' => $expense->monthly_amount,
        ]);

        $this->dispatch('planned-expense-saved');

        $this->reset();

        Flux::toast(
            variant: 'success',
            text: 'Expense successfully created',
        );

        Flux::modals()->close();
    }

    public function render(): View
    {
        return view('livewire.planned-spending-form');
    }
}
