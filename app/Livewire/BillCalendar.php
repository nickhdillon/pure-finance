<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Contracts\View\View;

class BillCalendar extends Component
{
    public function render(): View
    {
        return view('livewire.bill-calendar');
    }
}
