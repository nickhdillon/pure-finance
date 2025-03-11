<?php

declare(strict_types=1);

namespace App\Livewire;

use Flux\Flux;
use App\Models\Tag;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\Database\Eloquent\Builder;

class TagTable extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $tag_id): void
    {
        $tag = Tag::find($tag_id);

        Flux::toast(
            variant: 'success',
            text: "Successfully deleted the {$tag?->name} tag",
        );

        Flux::modals()->close();

        $tag?->delete();
    }

    #[On('tag-saved')]
    public function render(): View
    {
        return view('livewire.tag-table', [
            'tags' => auth()
                ->user()
                ->tags()
                ->select(['id', 'name'])
                ->when(strlen($this->search) >= 1, function (Builder $query): void {
                    $query->where('name', 'like', "%{$this->search}%");
                })
                ->orderBy('name')
                ->paginate(15)
        ]);
    }
}
