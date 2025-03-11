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
        $user_accounts = auth()->user()->accounts();

        return view('livewire.accounts', [
            'available_total' => $user_accounts->sum('balance'),
            'accounts' => $user_accounts->orderBy('name')->get()
        ]);
    }
}
