<?php

declare(strict_types=1);

namespace App\Livewire;

use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\PlannedExpense;
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

    public ?PlannedExpense $expense = null;

    public array $categories = [];

    protected function messages(): array
    {
        return [
            'category_id.required' => 'The category field is required.',
        ];
    }

    public function mount(): void
    {
        $this->getCategories();

        if ($this->expense) {
            $this->name = $this->expense->name;
            $this->category_id = $this->expense->category_id;
            $this->monthly_amount = $this->expense->monthly_amount;
        }
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

        if ($this->expense) {
            PlannedExpense::query()
                ->where('id', $this->expense['id'])
                ->update($validated_data);
        } else {
            auth()->user()->planned_expenses()->create($validated_data);
        }

        $this->dispatch('planned-expense-saved');

        if (! $this->expense) {
            $this->reset();
        }
        Flux::toast(
            variant: 'success',
            text: 'Expense successfully ' . ($this->expense ? 'updated' : 'created'),
        );

        Flux::modals()->close();
    }

    public function delete(): void
    {
        $this->expense?->delete();

        Flux::toast(
            variant: 'success',
            text: 'Expense successfully deleted',
        );

        $this->redirectRoute('planned-spending', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.planned-spending-form');
    }
}
