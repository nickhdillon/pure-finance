<?php

declare(strict_types=1);

namespace App\Livewire;

use Flux\Flux;
use Livewire\Component;
use App\Models\Category;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\Database\Eloquent\Builder;

class CategoryTable extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $category_id): void
    {
        $category = Category::find($category_id);

        Flux::toast(
            variant: 'success',
            text: "Successfully deleted the {$category?->name} category",
        );

        Flux::modals()->close();

        $category?->delete();
    }

    #[On('category-saved')]
    public function render(): View
    {
        return view('livewire.category-table', [
            'categories' => auth()
                ->user()
                ->categories()
                ->with('parent')
                ->select(['id', 'name', 'parent_id'])
                ->when(strlen($this->search) >= 1, function (Builder $query): void {
                    $query->where('name', 'like', "%{$this->search}%");
                })
                ->orderBy('name')
                ->paginate(15),
        ]);
    }
}
