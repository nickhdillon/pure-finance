<div class="space-y-4 mx-auto max-w-4xl">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">
            Categories
        </flux:heading>

        <div>
            <flux:modal.trigger name="category-form">
                <flux:button icon="plus" variant="primary" size="sm">
                    Add
                </flux:button>
            </flux:modal.trigger>
        </div>
    </div>

    <x-card>
        <x-slot:content>
            <div class="p-3">
                <flux:input size="sm" icon="magnifying-glass" placeholder="Search categories..." wire:model.live.debounce.300ms='search' clearable />
            </div>

            @if ($categories->count() > 0)
                <flux:table :paginate="$categories" class="border-t border-zinc-200 dark:border-white/20">
                    <flux:table.columns class="[&>tr>th]:px-3! bg-zinc-50 dark:bg-zinc-800">
                        <flux:table.column>
                            Name
                        </flux:table.column>

                        <flux:table.column>
                            Parent
                        </flux:table.column>

                        <flux:table.column class="[&>div]:justify-end!">
                            Actions
                        </flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows class="dark:bg-zinc-900">
                        @foreach ($categories as $category)
                            <flux:table.row :key="$category->id" class="[&>td]:px-3! [&>td]:py-2!">
                                <flux:table.cell class="whitespace-nowrap" variant="strong">
                                    {{ $category->name }}
                                </flux:table.cell>

                                <flux:table.cell class="whitespace-nowrap" variant="strong">
                                    {{ $category->parent?->name }}
                                </flux:table.cell>

                                <flux:table.cell class="[&>div]:justify-end!">
                                    <div class="flex items-center ">
                                        <div>
                                            <flux:modal.trigger name="category-form">
                                                <flux:button icon="pencil-square" variant="ghost" size="sm"
                                                    class="text-indigo-500!" x-on:click="$dispatch('load-category', { category: {{ $category }} })" />
                                            </flux:modal.trigger>
                                        </div>

                                        <div>
                                            <flux:modal.trigger name="delete-category-{{ $category->id }}">
                                                <flux:button icon="trash" variant="ghost" size="sm"
                                                    class="text-red-500!" />
                                            </flux:modal.trigger>

                                            <flux:modal name="delete-category-{{ $category->id }}" class="min-w-[22rem]">
                                                <form wire:submit="delete({{ $category->id }})" class="space-y-6">
                                                    <div class="space-y-4!">
                                                        <flux:heading size="lg" class="font-semibold -mt-1.5!">
                                                            Delete Category?
                                                        </flux:heading>

                                                        <flux:subheading>
                                                            Are you sure you want to delete the

                                                            <span class="font-semibold text-red-500">
                                                                '{{ $category->name }}'
                                                            </span>
            
                                                            category?
                                                        </flux:subheading>
                                                    </div>

                                                    <div class="flex gap-2">
                                                        <flux:spacer />

                                                        <flux:modal.close>
                                                            <flux:button variant="ghost" size="sm">
                                                                Cancel
                                                            </flux:button>
                                                        </flux:modal.close>

                                                        <flux:button type="submit" variant="danger" size="sm">
                                                            Confirm
                                                        </flux:button>
                                                    </div>
                                                </form>
                                            </flux:modal>
                                        </div>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @else
                <flux:heading class="italic! font-medium text-center pb-3">
                    No categories found...
                </flux:heading>
            @endif
        </x-slot:content>
    </x-card>

    <livewire:category-form />
</div>