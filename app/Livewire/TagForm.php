<?php

declare(strict_types=1);

namespace App\Livewire;

use Flux\Flux;
use App\Models\Tag;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class TagForm extends Component
{
    public ?Tag $tag = null;

    public string $name = '';

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'unique:tags,name,NULL,id,user_id,' . auth()->id()
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.unique' => 'The provided name has already been taken.'
        ];
    }

    public function mount(): void
    {
        if ($this->tag) $this->name = $this->tag->name;
    }

    public function submit(): void
    {
        $validated_data = $this->validate();

        if ($this->tag) {
            Tag::query()
                ->where('id', $this->tag['id'])
                ->update($validated_data);
        } else {
            auth()->user()->tags()->create($validated_data);
        }

        $this->dispatch('tag-saved');

        if (!$this->tag) $this->reset();

        Flux::toast(
            variant: 'success',
            text: "Tag successfully " . ($this->tag ? "updated" : "created"),
        );

        Flux::modals()->close();
    }

    public function render(): View
    {
        return view('livewire.tag-form');
    }
}
