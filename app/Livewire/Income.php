<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\IncomeType;
use App\Enums\TransactionType;
use App\Models\Transaction;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Income extends Component
{
    public ?int $deleting_income_id = null;

    public ?int $receiving_income_id = null;

    public ?int $receiving_account_id = null;

    public ?int $category_id = null;

    public array $categories = [];

    public function mount(): void
    {
        $this->getCategories();
    }

    #[On('category-saved')]
    public function getCategories(): void
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
    }

    #[On('set-category')]
    public function setCategory(): void
    {
        $this->category_id = auth()
            ->user()
            ->categories()
            ->with(['children:id,parent_id,name'])
            ->latest('id')
            ->select(['id', 'name'])
            ->first()
            ->id;
    }

    public function confirmDelete(int $income_id): void
    {
        $this->deleting_income_id = $income_id;
    }

    public function delete(int $income_id): void
    {
        auth()->user()->incomes()->findOrFail($income_id)?->delete();

        Flux::toast(variant: 'success', text: 'Income successfully deleted.');

        Flux::modal('delete-income')->close();
    }

    public function confirmReceipt(int $income_id): void
    {
        $income = auth()->user()->incomes()->findOrFail($income_id);

        if ($income->received) {
            return;
        }

        $this->receiving_income_id = $income->id;
        $this->receiving_account_id = null;
        $this->category_id = null;

        $this->resetValidation(['receiving_account_id', 'category_id']);
    }

    public function markReceived(): void
    {
        $validated = $this->validate([
            'receiving_income_id' => [
                'required',
                'integer',
                Rule::exists('incomes', 'id')->where('user_id', auth()->id())
            ],
            'receiving_account_id' => [
                'required',
                'integer',
                Rule::exists('accounts', 'id')->where('user_id', auth()->id())
            ],
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where('user_id', auth()->id())
            ],
        ]);

        $income = auth()->user()->incomes()->findOrFail($validated['receiving_income_id']);

        if ($income->received) {
            Flux::modal('mark-income-received')->close();

            return;
        }

        DB::transaction(function () use ($income, $validated): void {
            $transaction = Transaction::create([
                'account_id' => $validated['receiving_account_id'],
                'category_id' => $validated['category_id'],
                'type' => TransactionType::DEPOSIT,
                'amount' => $income->amount,
                'payee' => $income->name,
                'date' => today('America/Chicago')->toDateString(),
                'notes' => $income->notes,
                'status' => true,
            ]);

            $income->update([
                'received' => true,
                'transaction_id' => $transaction->id,
            ]);
        });

        Flux::toast(variant: 'success', text: 'Income marked received and transaction created.');

        Flux::modal('mark-income-received')->close();
    }

    #[Computed]
    public function incomeCards(): array
    {
        $incomes = auth()
            ->user()
            ->incomes()
            ->latest('date')
            ->latest('id')
            ->get();

        $expected = $incomes
            ->where('type', IncomeType::EXPECTED)
            ->values();

        $unplanned = $incomes
            ->where('type', IncomeType::UNPLANNED)
            ->values();

        $cards = [
            [
                'name' => 'Expected',
                'total' => $expected->sum('amount'),
                'incomes' => $expected,
            ],
        ];

        if ($unplanned->isNotEmpty()) {
            $cards[] = [
                'name' => 'Unplanned',
                'total' => $unplanned->sum('amount'),
                'incomes' => $unplanned,
            ];
        }

        return $cards;
    }

    #[Computed]
    public function incomeTotal(): float|int
    {
        return collect($this->incomeCards)->sum('total');
    }

    #[On('income-saved')]
    public function render(): View
    {
        return view('livewire.income', [
            'accounts' => auth()->user()->accounts()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
