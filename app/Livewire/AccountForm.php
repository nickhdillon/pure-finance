<?php

declare(strict_types=1);

namespace App\Livewire;

use Flux\Flux;
use App\Models\Account;
use Livewire\Component;
use App\Enums\AccountType;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\View\View;

class AccountForm extends Component
{
    public ?Account $account = null;

    public string $name;

    public AccountType $type;

    public ?float $initial_balance = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'type' => ['required', Rule::enum(AccountType::class)],
            'initial_balance' => ['nullable', 'decimal:0,2', 'numeric'],
        ];
    }

    public function mount(): void
    {
        if ($this->account) {
            $this->name = $this->account->name;
            $this->type = $this->account->type;
            $this->initial_balance = $this->account->initial_balance;
        }
    }

    public function submit(): void
    {
        $validated_data = $this->validate();

        $this->account
            ? $this->account->update($validated_data)
            : auth()->user()->accounts()->create($validated_data);

        if (!$this->account) $this->reset();

        Flux::toast(
            variant: 'success',
            text: "Account successfully " . ($this->account ? "updated" : "created"),
            position: 'top-right'
        );

        Flux::modals()->close();

        $this->dispatch('account-saved');
    }

    public function render(): View
    {
        return view('livewire.account-form');
    }
}
