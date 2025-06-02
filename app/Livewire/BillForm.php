<?php

declare(strict_types=1);

namespace App\Livewire;

use Flux\Flux;
use Carbon\Carbon;
use App\Models\Bill;
use Livewire\Component;
use App\Enums\BillColor;
use Livewire\Attributes\On;
use Illuminate\Validation\Rule;
use App\Enums\RecurringFrequency;
use Illuminate\Contracts\View\View;

class BillForm extends Component
{
    public ?Bill $bill = null;

    public array $categories = [];

    public array $colors = [];

    public string $name = '';

    public int $category_id;

    public float $amount;

    public ?Carbon $date = null;

    public ?RecurringFrequency $frequency = null;

    public ?string $notes = null;

    public BillColor $color = BillColor::GREEN;

    public bool $paid = false;

    public ?array $attachments = [];

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
            'color' => [
                'nullable',
                'required',
                Rule::enum(BillColor::class),
            ],
            'paid' => ['required', 'boolean'],
            'attachments' => ['nullable', 'array'],
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
        $this->getCategories()
            ->getColors();

        $this->date = today('America/Chicago');

        $this->color = BillColor::GREEN;
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
        $this->color = $this->bill->color;
        $this->paid = $this->bill->paid;
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
            'color',
            'paid'
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

    public function getColors(): self
    {
        $this->colors = BillColor::cases();

        return $this;
    }

    #[On('file-uploaded')]
    public function pushToAttachments(array $file): void
    {
        $this->attachments[] = $file;
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
