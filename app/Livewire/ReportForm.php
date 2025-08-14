<?php

declare(strict_types=1);

namespace App\Livewire;

use Flux\Flux;
use App\Models\Tag;
use App\Models\Report;
use Prism\Prism\Prism;
use App\Models\Account;
use Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Enums\TransactionType;
use Prism\Prism\Enums\Provider;
use Illuminate\Validation\Rule;
use App\Models\ReportTransaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class ReportForm extends Component
{
    public Collection $accounts;

    public array $categories = [];

    public array $tags = [];

    public array $user_payees = [];

    public array $transaction_types = [];

    public string $name = '';

    public ?int $account_id = null;

    public ?int $category_id = null;

    public ?int $tag_id = null;

    public ?TransactionType $type = null;

    public array $payees = [];

    public array $date_range;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'account_id' => ['nullable', 'int'],
            'type' => ['nullable', Rule::enum(TransactionType::class)],
            'payees' => ['nullable', 'array'],
            'category_id' => ['nullable', 'int'],
            'tag_id' => ['nullable', 'int'],
            'date_range' => ['required']
        ];
    }

    public function mount(): void
    {
        $this->getAccounts()
            ->getCategories()
            ->getTransactionTypes()
            ->getTags()
            ->getPayees();
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

    public function getTransactionTypes(): self
    {
        $this->transaction_types = collect(TransactionType::cases())
            ->sortBy('value')
            ->values()
            ->all();

        return $this;
    }

    public function getTags(): self
    {
        $this->tags = auth()
            ->user()
            ->tags()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get()
            ->toArray();

        return $this;
    }

    public function getPayees(): self
    {
        $this->user_payees = auth()
            ->user()
            ->transactions()
            ->select(['transactions.id', 'transactions.payee'])
            ->orderBy('payee')
            ->pluck('payee')
            ->map(fn(string $payee): string => trim($payee))
            ->unique()
            ->toArray();

        return $this;
    }

    private function createReportAndTransactions(EloquentCollection $transactions): Report
    {
        $validated_data = $this->validate();

        $report = auth()->user()->reports()->create([
            ...$validated_data,
            'name' => Str::limit($this->name, 25),
            'start_date' => $this->date_range['start'],
            'end_date' => $this->date_range['end']
        ]);

        $report_transactions = [];

        foreach ($transactions as $transaction) {
            $report_transactions[] = [
                'report_id' => $report->id,
                'account_id' => $transaction->account_id,
                'category_id' => $transaction->category_id,
                'type' => $transaction->type,
                'payee' => html_entity_decode($transaction->payee),
                'amount' => $transaction->amount,
                'date' => $transaction->date,
                'snapshot' => json_encode($transaction->toArray()),
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        ReportTransaction::insert($report_transactions);

        return $report;
    }

    public function submit(): void
    {
        $account_name = Account::find($this->account_id)?->name;

        $category_name = Category::find($this->category_id)?->name;

        $tag_name = Tag::find($this->tag_id)?->name;

        $this->name = Prism::text()
            ->using(Provider::OpenAI, 'gpt-4.1-nano-2025-04-14')
            ->withPrompt("Generate a name for a report based on the following information if available (not null): account: {$account_name}, category: {$category_name}, tag: {$tag_name}, date range: {$this->date_range['start']} - {$this->date_range['end']}. Keep it short, with a limit of 25 characters. Exclude the word report, summary, overview, or anything similar, and make it human readable and helpful. It should be a good summary of what the report is without being too lengthy or too short to where it does not make much sense. Don't give me a response that is more than 25 characters no matter what.")
            ->asText()
            ->text;

        $transactions = auth()
            ->user()
            ->transactions()
            ->with(['account', 'category', 'tags'])
            ->when($this->account_id, function (Builder $query): void {
                $query->whereRelation('account', 'id', $this->account_id);
            })
            ->when($this->type, function (Builder $query): void {
                $query->where('transactions.type', $this->type);
            })
            ->when(! empty($this->payees), function (Builder $query): void {
                $query->whereIn('transactions.payee', $this->payees);
            })
            ->when($this->category_id, function (Builder $query): void {
                $query->whereRelation('category', 'id', $this->category_id);
            })
            ->when($this->tag_id, function (Builder $query): void {
                $query->whereRelation('tags', 'tag_id', $this->tag_id);
            })
            ->whereBetween('transactions.date', $this->date_range)
            ->latest('date')
            ->get();

        if (! app()->environment('testing')) {
            $report = DB::transaction(function () use ($transactions): Report {
                return $this->createReportAndTransactions($transactions);
            });
        } else {
            $report = $this->createReportAndTransactions($transactions);
        }

        Flux::toast(
            variant: 'success',
            text: "Successfully created report",
        );

        $this->redirectRoute('report-view', $report, navigate: true);
    }

    public function render(): View
    {
        return view('livewire.report-form');
    }
}
