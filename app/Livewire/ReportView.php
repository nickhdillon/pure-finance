<?php

declare(strict_types=1);

namespace App\Livewire;

use Flux\Flux;
use App\Models\Report;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class ReportView extends Component
{
    use WithPagination;

    public Report $report;

    public string $search = '';

    #[Validate(['required', 'string'])]
    public string $name;

    public function mount(): void
    {
        $this->name = $this->report->name;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function submit(): void
    {
        $this->report->update($this->validate());

        Flux::toast(
            variant: 'success',
            text: 'Report successfully updated',
        );

        Flux::modals()->close();
    }

    public function render(): View
    {
        return view('livewire.report-view', [
            'report_transactions' => $this->report->transactions()
                ->with(['account', 'category.parent'])
                ->when(strlen($this->search) >= 1, function (Builder $query): void {
                    $query->where(function (Builder $query): void {
                        $query->whereRelation('account', 'name', 'like', "%{$this->search}%")
                            ->orWhereRelation('category', 'name', 'like', "%{$this->search}%")
                            ->orWhere('payee', 'like', "%{$this->search}%")
                            ->orWhere('amount', 'like', "%{$this->search}%")
                            ->orWhere('type', 'like', "%{$this->search}%");
                    });
                })
                ->latest('date')
                ->paginate(25)
        ]);
    }
}
