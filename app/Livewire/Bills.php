<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class Bills extends Component
{
    #[Url(except: 'list')]
    public string $view = 'list';

    public function render(): View
    {
        if (! in_array($this->view, ['list', 'calendar'], true)) {
            $this->view = 'list';
        }

        $bills = auth()->user()->bills()->orderBy('date')->orderBy('name')->get();

        return view('livewire.bills', [
            'bills' => $bills->map(function (Bill $bill): array {
                return [
                    ...$bill->toArray(),
                    'date' => Carbon::parse($bill->date)->toDateString(),
                ];
            }),
            'bill_groups' => $bills->groupBy(fn (Bill $bill): string => $bill->date->toDateString()),
            'bill_total' => $bills->sum('amount'),
        ]);
    }
}
