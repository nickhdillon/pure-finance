<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Contracts\View\View;

class SavingsGoals extends Component
{
    public function render(): View
    {
        return view('livewire.savings-goals', [
            'savings_goals' => auth()
                ->user()
                ->savings_goals()
                ->orderBy('name')
                ->get()
        ]);
    }
}
