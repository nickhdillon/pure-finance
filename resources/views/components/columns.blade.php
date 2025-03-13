@props(['columns'])

<div wire:ignore class="hidden sm:flex">
    <flux:dropdown>
        <flux:button icon="view-columns" variant="ghost" />

        <flux:menu x-cloak class="p-4! w-[250px]">
            <flux:heading class="mb-3 font-medium" size="lg">
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

    {{-- <div x-cloak x-show="columnsModalOpen" x-on:keydown.window.escape="columnsModalOpen = false"
        x-on:click.away="columnsModalOpen = false"
        class="px-5 p-4 bg-white dark:bg-slate-800 rounded-xl z-10 w-[275px] border shadow-md border-slate-200 dark:border-slate-600"
        x-anchor.bottom-end="$refs.columns">
        <h1 class="mb-3 font-semibold text-slate-800 dark:text-slate-200">
            Columns
        </h1>

        <div class="space-y-4">
            @foreach ($columns as $column)
                <flux:checkbox wire:model.live="columns" label="{{ Str::title($column) }}"
                    value="{{ $column }}" />
            @endforeach
        </div>
    </div> --}}
</div>

