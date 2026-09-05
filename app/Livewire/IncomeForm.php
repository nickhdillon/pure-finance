<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\IncomeType;
use App\Models\Income;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class IncomeForm extends Component
{
    public bool $show_income_form = false;

    public ?Income $income = null;

    public string $name = '';

    public string $amount = '';

    public IncomeType $type = IncomeType::EXPECTED;

    public string $date = '';

    public ?string $notes = null;

    public bool $received = false;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'decimal:0,2', 'numeric', 'gt:0'],
            'type' => ['required', Rule::enum(IncomeType::class)],
            'date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'received' => ['required', 'boolean'],
        ];
    }

    public function mount(): void
    {
        $this->date = today('America/Chicago')->toDateString();
    }

    #[On('load-income')]
    public function loadIncome(int $income_id): void
    {
        $this->income = auth()->user()->incomes()->findOrFail($income_id);
        $this->name = $this->income->name;
        $this->amount = number_format($this->income->amount, 2, '.', '');
        $this->type = $this->income->type;
        $this->date = $this->income->date->toDateString();
        $this->notes = $this->income->notes;
        $this->received = $this->income->received;
        $this->show_income_form = true;
    }

    public function resetForm(): void
    {
        $this->reset(['income', 'name', 'amount', 'notes', 'received']);
        $this->type = IncomeType::EXPECTED;
        $this->date = today('America/Chicago')->toDateString();
        $this->resetValidation();
    }

    public function submit(): void
    {
        $validated_data = $this->validate();
        $was_editing = (bool) $this->income;

        if ($was_editing) {
            $this->income->update($validated_data);
        } else {
            auth()->user()->incomes()->create($validated_data);
        }

        Flux::toast(
            variant: 'success',
            text: 'Income successfully '.($was_editing ? 'updated' : 'created'),
        );

        $this->resetForm();
        Flux::modals()->close();
        $this->dispatch('income-saved');
    }

    public function render(): View
    {
        return view('livewire.income-form');
    }
}
