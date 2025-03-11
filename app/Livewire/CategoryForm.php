<?php

declare(strict_types=1);

namespace App\Livewire;

use Flux\Flux;
use Livewire\Component;
use App\Models\Category;
use Livewire\Attributes\On;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;

class CategoryForm extends Component
{
    public ?array $category = null;

    public ?int $parent_id = null;

    public string $name = '';

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'unique:categories,name,NULL,id,user_id,' . auth()->id()
            ],
            'parent_id' => [
                'nullable',
                'integer',
                'numeric',
                Rule::in($this->parent_categories->pluck('id')->toArray())
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.unique' => 'The provided name has already been taken.',
            'parent_id.integer' => 'The parent category must be an integer.',
            'parent_id.in' => 'The selected parent category is invalid.',
        ];
    }

    #[On('load-category')]
    public function loadCategory(array $category): void
    {
        $this->category = $category;
        $this->name = $this->category['name'];
        $this->parent_id = $this->category['parent_id'];
    }

    public function resetForm(): void
    {
        $this->reset(['category', 'name', 'parent_id']);
    }

    #[Computed]
    public function parentCategories(): Collection
    {
        return auth()
            ->user()
            ->categories()
            ->select(['id', 'name'])
            ->whereNull('parent_id')
            ->get();
    }

    public function submit(): void
    {
        $validated_data = $this->validate();

        if ($this->category) {
            Category::query()
                ->where('id', $this->category['id'])
                ->update($validated_data);
        } else {
            auth()->user()->categories()->create($validated_data);
        }

        $this->dispatch('category-saved');

        if (!$this->category) $this->reset(['name', 'parent_id']);

        Flux::toast(
            variant: 'success',
            text: "Category successfully " . ($this->category ? "updated" : "created"),
            position: 'top-right'
        );

        Flux::modals()->close();
    }

    public function render(): View
    {
        return view('livewire.category-form');
    }
}
