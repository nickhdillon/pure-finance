<?php

declare(strict_types=1);

namespace App\Livewire;

use Flux\Flux;
use App\Models\Report;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class Reports extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $report_id): void
    {
        $report = Report::find($report_id);

        Flux::toast(
            variant: 'success',
            text: "Successfully deleted the {$report?->name} report",
        );

        Flux::modals()->close();

        $report?->delete();
    }

    public function render(): View
    {
        return view('livewire.reports', [
            'reports' => auth()
                ->user()
                ->reports()
                ->with(['account', 'category', 'tag'])
                ->when(strlen($this->search) >= 1, function (Builder $query): void {
                    $query->where('name', 'like', "%{$this->search}%");
                })
                ->latest('id')
                ->paginate(25)
        ]);
    }
}
