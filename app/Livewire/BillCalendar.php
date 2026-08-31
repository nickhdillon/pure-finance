<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class BillCalendar extends Component
{
    #[Url(except: 'calendar')]
    public string $view = 'calendar';

    public function render(): View
    {
        if (! in_array($this->view, ['calendar', 'list'], true)) {
            $this->view = 'calendar';
        }

        $bills = auth()->user()->bills()->orderBy('date')->orderBy('name')->get();

        return view('livewire.bill-calendar', [
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
