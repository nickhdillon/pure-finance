@props(['user_tags', 'disabled' => null])

<flux:field>
    <flux:label @class([
        'opacity-50' => $disabled
    ])>
        Tags
    </flux:label>

    <flux:select variant="listbox" searchable multiple placeholder="Select tags" clearable x-model="$wire.tags">
        <x-slot name="search" class="relative">
            <flux:select.search form class="px-4" placeholder="Search tags..." />

            <div class="absolute top-1 right-0 pr-1">
                <flux:modal.trigger name="add-tag">
                    <flux:button square variant="subtle" size="sm" aria-label="Tag form">
                        <flux:icon.plus variant="micro" />
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </x-slot>
        
        @foreach ($user_tags as $tag)
            <flux:select.option value="{{ $tag }}" class="font-semibold">
                {{ $tag }}
            </flux:select.option>
        @endforeach
    </flux:select>

    <flux:error name="tags" />

    <div class="absolute">
        <livewire:tag-form />
    </div>

    <div x-cloak x-show="$wire.tags.length" class="flex flex-wrap gap-1.5">
        <template x-for="(tag, index) in $wire.tags" :key="index">
            <flux:badge color="emerald">
                <p x-text="tag"></p> 
                <flux:badge.close x-on:click="$wire.tags.splice(index, 1)" />
            </flux:badge>
        </template>
    </div>
</flux:field>
