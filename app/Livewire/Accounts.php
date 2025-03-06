<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Contracts\View\View;

#[On('account-saved'), On('status-changed')]
class Accounts extends Component
{
    public function render(): View
    {
        return view('livewire.accounts', [
            'accounts' => auth()
                ->user()
                ->accounts()
                ->orderBy('name')
                ->get()
        ]);
    }
}
