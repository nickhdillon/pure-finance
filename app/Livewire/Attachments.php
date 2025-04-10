<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Contracts\View\View;

class Attachments extends Component
{
    public bool $show_attachments = false;

    public array $attachments = [];

    #[On('load-attachments')]
    public function loadAttachments(?array $attachments = null): void
    {
        $this->attachments = $attachments;
    }

    public function resetAttachments(): void
    {
        $this->reset('attachments');
    }

    public function render(): View
    {
        return view('livewire.attachments');
    }
}
