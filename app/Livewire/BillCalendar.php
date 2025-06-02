<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Bill;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class BillCalendar extends Component
{
    public function render(): View
    {
        return view('livewire.bill-calendar', [
            'bills' => auth()->user()->bills->map(function (Bill $bill): array {
                return [
                    ...$bill->toArray(),
                    'bgColor' => $bill->color->bgColor(),
                    'textColor' => $bill->color->textColor(),
                ];
            })
        ]);
    }
}
