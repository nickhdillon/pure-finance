<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Contracts\View\View;

class UpcomingBills extends Component
{
    public function render(): View
    {
        $now = now('America/Chicago');
        $start = $now->copy();
        $end = $now->copy()->endOfWeek()->subDay();

        return view('livewire.upcoming-bills', [
            'today' => $start->format('n/d/y'),
            'end_of_week' => $end->format('n/d/y'),
            'upcoming_bills' => auth()
                ->user()
                ->bills()
                ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                ->orderBy('date')
                ->get()
        ]);
    }
}
