@props(['columns'])

<div wire:ignore class="hidden sm:block">
    <flux:dropdown>
        <flux:button icon="view-columns" variant="ghost" />

        <flux:menu x-cloak class="p-5! w-[250px]">
            <flux:heading class="mb-3 -mt-1 font-medium" size="lg">
                Columns
            </flux:heading>

            <div class="space-y-4">
                @foreach ($columns as $column)
                    <flux:checkbox wire:model.live="columns" label="{{ Str::title($column) }}"
                        value="{{ $column }}" />
                @endforeach
            </div>
        </flux:menu>
    </flux:dropdown>
</div>
