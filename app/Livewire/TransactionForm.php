<?php

declare(strict_types=1);

namespace App\Livewire;

use Flux\Flux;
use Carbon\Carbon;
use App\Models\Tag;
use Livewire\Component;
use App\Models\Account;
use App\Models\Transaction;
use Livewire\Attributes\On;
use App\Enums\TransactionType;
use App\Actions\CreateTransfer;
use Illuminate\Validation\Rule;
use App\Enums\RecurringFrequency;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use App\Rules\FrequencyIntervalRule;
use Illuminate\Http\RedirectResponse;
use App\Actions\CreateRecurringTransactions;
use Livewire\Features\SupportRedirects\Redirector;

class TransactionForm extends Component
{
    public ?Account $account = null;

    public ?Transaction $transaction = null;

    public Collection $accounts;

    public array $categories = [];

    public ?int $account_id = null;

    public ?int $transfer_to = null;

    public string $payee = '';

    public array $transaction_types = [];

    public ?TransactionType $type = null;

    public float $amount;

    public int $category_id;

    public ?Carbon $date = null;

    public array $user_tags = [];

    public array $tags = [];

    public ?string $notes = null;

    public ?array $attachments = [];

    public bool $status = false;

    public bool $is_recurring = false;

    public ?RecurringFrequency $frequency = null;

    public ?Carbon $recurring_end = null;

    protected function rules(): array
    {
        return [
            'account_id' => ['required', 'int', 'different:transfer_to'],
            'payee' => ['required', 'string'],
            'type' => ['required', Rule::enum(TransactionType::class)],
            'transfer_to' => [
                'nullable',
                'int',
                'different:account_id',
                Rule::requiredIf(fn(): bool => $this->type === TransactionType::TRANSFER),
            ],
            'amount' => ['required', 'decimal:0,2', 'numeric'],
            'category_id' => ['required', 'int'],
            'date' => ['required', 'date'],
            'tags' => ['nullable', 'array'],
            'notes' => ['nullable', 'string'],
            'attachments' => ['nullable', 'array'],
            'status' => ['required', 'boolean'],
            'is_recurring' => ['required', 'boolean'],
            'frequency' => [
                'nullable',
                'required_if:is_recurring,true',
                Rule::enum(RecurringFrequency::class),
            ],
            'recurring_end' => array_filter([
                'nullable',
                'date',
                $this->is_recurring ?
                    new FrequencyIntervalRule($this->date, $this->recurring_end, $this->frequency) :
                    null,
            ]),
        ];
    }

    protected function messages(): array
    {
        return [
            'account_id.required' => 'The account field is required.',
            'category_id.required' => 'The category field is required.',
        ];
    }

    public function mount(): void
    {
        $this->getAccounts()
            ->getCategories()
            ->getTransactionTypes()
            ->getUserTags();

        $this->date = today('America/Chicago');

        if ($this->account) {
            $this->account_id = $this->account->id;
        }

        if ($this->transaction) {
            $this->account_id = $this->transaction->account->id;
            $this->account = $this->transaction->account;
            $this->payee = $this->transaction->payee;
            $this->type = $this->transaction->type;
            $this->transfer_to = $this->transaction->transfer_to;
            $this->amount = $this->transaction->amount;
            $this->category_id = $this->transaction->category_id;
            $this->date = $this->transaction->date;
            $this->tags = $this->transaction->tags->pluck('name')->toArray();
            $this->notes = $this->transaction->notes;
            $this->status = $this->transaction->status;
            $this->is_recurring = $this->transaction->is_recurring;
            $this->frequency = $this->transaction->frequency;
            $this->recurring_end = $this->transaction->recurring_end;
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

    #[On('category-saved')]
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

    #[On('set-category')]
    public function setCategory(): self
    {
        $this->category_id = auth()
            ->user()
            ->categories()
            ->with(['children:id,parent_id,name'])
            ->latest('id')
            ->select(['id', 'name'])
            ->first()
            ->id;

        return $this;
    }

    public function getTransactionTypes(): self
    {
        $this->transaction_types = collect(TransactionType::cases())
            ->sortBy('value')
            ->values()
            ->all();

        return $this;
    }

    #[On('tag-saved')]
    public function getUserTags(): self
    {
        $this->user_tags = auth()
            ->user()
            ->tags()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->pluck('name')
            ->toArray();

        return $this;
    }

    #[On('set-tags')]
    public function pushTag(): self
    {
        $tag = auth()
            ->user()
            ->tags()
            ->latest('id')
            ->select(['id', 'name'])
            ->first();

        $this->tags[] = $tag->name;

        return $this;
    }

    public function updatedTransferTo(): void
    {
        if ($account = Account::find($this->transfer_to)?->name) {
            $this->payee = $account;
        }
    }

    #[On('file-uploaded')]
    public function pushToAttachments(array $file): void
    {
        $this->attachments[] = $file;
    }

    #[On('file-deleted')]
    public function deleteAttachment(string $file_id): void
    {
        if ($this->transaction) {
            $this->transaction->attachments = collect($this->transaction->attachments)
                ->reject(fn(array $attachment): bool => $attachment['id'] === $file_id)
                ->values()
                ->all();

            $this->transaction->save();
        }
    }

    public function delete(Transaction $transaction): void
    {
        $transaction->delete();

        Flux::toast(
            variant: 'success',
            text: 'Successfully deleted transaction',
        );

        Flux::modals()->close();

        $this->redirectRoute('account-overview', $this->account);
    }

    public function submit(
        CreateTransfer $transfer_action,
        CreateRecurringTransactions $recurring_action
    ): RedirectResponse|Redirector {
        $validated_data = $this->validate();

        if ($this->transaction) {
            $validated_data['attachments'] = [
                ...$this->transaction->attachments ?? [],
                ...$this->attachments ?? [],
            ];
        } else {
            $validated_data['attachments'] = $this->attachments;
        }

        $current_tags = Tag::whereIn('name', $this->tags)->pluck('id')->toArray();

        if ($this->transaction) {
            $this->transaction->tags()->sync($current_tags);

            $this->transaction->update($validated_data);

            if (!$this->status) $this->transaction->bill?->update(['paid' => false]);
        } else {
            $new_transaction = auth()->user()->transactions()->create($validated_data);

            $new_transaction->tags()->sync($current_tags);
        }

        if ($this->transfer_to) {
            $transfer_action->handle(Account::find($this->transfer_to), $this->transaction ?: $new_transaction);
        }

        if (
            (! $this->transaction || $this->transaction->children->count() === 0)
            && $this->frequency !== RecurringFrequency::ONE_TIME
        ) {
            $recurring_action->handle($this->transaction ?: $new_transaction);
        }

        Flux::toast(
            variant: 'success',
            text: 'Transaction successfully ' . ($this->transaction ? 'updated' : 'created'),
        );

        return redirect()->route('account-overview', $this->account ?? Account::find($this->account_id));
    }

    public function render(): View
    {
        return view('livewire.transaction-form');
    }
}
