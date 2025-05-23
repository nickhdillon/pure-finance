@props(['categories', 'disabled' => null])

<flux:field>
    <flux:label @class([
        'opacity-50' => $disabled
    ])>
        Category
    </flux:label>

    <flux:select variant="listbox" searchable placeholder="Select a category" clearable wire:model='category_id'>
        <x-slot name="search" class="relative">
            <flux:select.search form class="px-4" placeholder="Search categories..." />

            <div class="absolute top-1 right-0 pr-1">
                <flux:modal.trigger name="category-form">
                    <flux:button square variant="subtle" size="sm" aria-label="Category form">
                        <flux:icon.plus variant="micro" />
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </x-slot>
        
        @foreach ($categories as $category)
            <flux:select.option value="{{ $category['id'] }}" class="font-semibold">
                {{ $category['name'] }}
            </flux:select.option>

            @foreach($category['children'] as $child)
                <flux:select.option value="{{ $child['id'] }}" class="pl-7.5">
                    {{ $child['name'] }}
                </flux:select.option>
            @endforeach
        @endforeach
    </flux:select>

    <flux:error name="category_id" />

    <div class="absolute">
        <livewire:category-form />
    </div>
</flux:field>