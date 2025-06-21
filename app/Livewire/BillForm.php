<?php

declare(strict_types=1);

namespace App\Livewire;

use Closure;
use Flux\Flux;
use Carbon\Carbon;
use App\Models\Bill;
use Livewire\Component;
use App\Enums\BillAlert;
use Livewire\Attributes\On;
use Illuminate\Validation\Rule;
use App\Enums\RecurringFrequency;
use App\Enums\TransactionType;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;

class BillForm extends Component
{
    public bool $show_bill_form = false;

    public ?Bill $bill = null;

    public array $categories = [];

    public array $colors = [];

    public Collection $times;

    public string $name = '';

    public int $category_id;

    public float $amount;

    public ?Carbon $date = null;

    public ?RecurringFrequency $frequency = null;

    public ?string $notes = null;

    public bool $paid = false;

    public ?array $attachments = [];

    public ?BillAlert $first_alert = null;

    public ?string $first_alert_time = null;

    public ?BillAlert $second_alert = null;

    public ?string $second_alert_time = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'category_id' => ['required', 'int'],
            'amount' => ['required', 'decimal:0,2', 'numeric'],
            'date' => ['required', 'date'],
            'frequency' => [
                'nullable',
                'required',
                Rule::enum(RecurringFrequency::class),
            ],
            'notes' => ['nullable', 'string'],
            'paid' => ['required', 'boolean'],
            'attachments' => ['nullable', 'array'],
            'first_alert' => ['nullable', 'required_with:first_alert_time'],
            'first_alert_time' => ['nullable', 'required_with:first_alert'],
            'second_alert' => ['nullable', 'required_with:second_alert_time', $this->validateSecondAlert()],
            'second_alert_time' => ['nullable', 'required_with:second_alert', $this->validateSecondAlert('time')],
        ];
    }

    protected function messages(): array
    {
        return [
            'category_id.required' => 'The category field is required.',
        ];
    }

    protected function validateSecondAlert(?string $time = null): Closure
    {
        return function ($attribute, $value, $fail) use ($time) {
            if ($value && (!$this->first_alert || !$this->first_alert_time)) {
                $fail('The first alert and time must be filled if adding a second alert' . ($time ? " {$time}." : '.'));
            }
        };
    }

    public function mount(): void
    {
        $this->getCategories()
            ->getTimes();

        $this->date = today('America/Chicago');
    }

    #[On('load-bill')]
    public function loadBill(int $bill_id): void
    {
        $this->bill = Bill::find($bill_id);
        $this->name = $this->bill->name;
        $this->category_id = $this->bill->category_id;
        $this->amount = $this->bill->amount;
        $this->date = $this->bill->date;
        $this->frequency = $this->bill->frequency;
        $this->notes = $this->bill->notes;
        $this->paid = $this->bill->paid;
        $this->attachments = $this->bill->attachments;
        $this->first_alert = $this->bill->first_alert;
        $this->first_alert_time = $this->bill->first_alert_time;
        $this->second_alert = $this->bill->second_alert;
        $this->second_alert_time = $this->bill->second_alert_time;

        $this->show_bill_form = true;
    }

    public function resetForm(): void
    {
        $this->reset([
            'bill',
            'name',
            'category_id',
            'amount',
            'date',
            'frequency',
            'notes',
            'paid',
            'attachments',
            'first_alert',
            'first_alert_time',
            'second_alert',
            'second_alert_time'
        ]);

        $this->date = today('America/Chicago');
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

    public function getTimes(): self
    {
        $this->times = collect(range(0, 23))->map(
            fn(int $hour): string => Carbon::createFromTime($hour, 0)->format('g A')
        );

        return $this;
    }

    #[On('file-uploaded')]
    public function pushToAttachments(array $file): void
    {
        $this->attachments[] = $file;
    }

    #[On('file-deleted')]
    public function deleteAttachment(string $file_id): void
    {
        if ($this->bill) {
            $this->bill->attachments = collect($this->bill->attachments)
                ->reject(fn(array $attachment): bool => $attachment['id'] === $file_id)
                ->values()
                ->all();

            $this->bill->save();
        }
    }

    public function changePaidStatus(): void
    {
        if ($this->paid) {
            $this->bill->update(['paid' => false]);

            $this->bill->transaction?->delete();
        } else {
            $this->bill->update(['paid' => true]);

            $this->bill->transaction()->create([
                'user_id' => auth()->id(),
                'category_id' => $this->category_id,
                'type' => TransactionType::DEBIT,
                'amount' => $this->amount,
                'payee' => $this->name,
                'date' => $this->date,
                'notes' => $this->notes,
                'attachments' => $this->attachments,
                'status' => true,
            ]);
        }

        Flux::toast(
            variant: 'success',
            text: 'Bill successfully ' . ($this->bill ? 'updated' : 'created'),
        );

        Flux::modal('bill-form')->close();

        $this->redirectRoute('bill-calendar', navigate: true);
    }

    public function submit(): void
    {
        $validated_data = $this->validate();

        if ($this->bill) {
            $this->bill->update($validated_data);
        } else {
            auth()->user()->bills()->create($validated_data);
        }

        if (! $this->bill) $this->reset();

        Flux::toast(
            variant: 'success',
            text: 'Bill successfully ' . ($this->bill ? 'updated' : 'created'),
        );

        Flux::modal('bill-form')->close();

        $this->redirectRoute('bill-calendar', navigate: true);
    }

    public function delete(Bill $bill): void
    {
        $bill->delete();

        Flux::toast(
            variant: 'success',
            text: 'Successfully deleted bill',
        );

        Flux::modals()->close();
    }

    public function render(): View
    {
        return view('livewire.bill-form');
    }
}
