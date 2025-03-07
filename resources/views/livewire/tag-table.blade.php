<div class="space-y-4">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">
            Tags
        </flux:heading>

        <div>
            <flux:modal.trigger name="add-tag">
                <flux:button icon="plus" variant="primary" size="sm">
                    Add
                </flux:button>
            </flux:modal.trigger>
            
            <livewire:tag-form data-name="add-tag" />
        </div>
    </div>

    <x-headerless-card>
        <div>
            <div class="p-3">
                <flux:input icon="magnifying-glass" placeholder="Search tags ..." wire:model.live='search' clearable />
            </div>

            <flux:table :paginate="$tags" class="border-t border-zinc-200 dark:border-white/20">
                <flux:table.columns class="[&>tr>th]:px-3! bg-zinc-50 dark:bg-white/5">
                    <flux:table.column>
                        Name
                    </flux:table.column>

                    <flux:table.column class="[&>div]:justify-end!">
                        Actions
                    </flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($tags as $tag)
                        <flux:table.row :key="$tag->id" class="[&>td]:px-3!">
                            <flux:table.cell class="whitespace-nowrap">
                                {{ $tag->name }}
                            </flux:table.cell>

                            <flux:table.cell class="[&>div]:justify-end!">
                                <div class="flex items-center ">
                                    <div>
                                        <flux:modal.trigger name="edit-tag-{{ $tag->id }}">
                                            <flux:button icon="pencil-square" variant="ghost" size="sm"
                                                class="text-indigo-500!" />
                                        </flux:modal.trigger>
                
                                        <livewire:tag-form data-name="edit-tag-{{ $tag->id }}" :$tag :key="$tag->id" />
                                    </div>

                                    <div>
                                        <flux:modal.trigger name="delete-tag-{{ $tag->id }}">
                                            <flux:button icon="trash" variant="ghost" size="sm"
                                                class="text-red-500!" />
                                        </flux:modal.trigger>

                                        <flux:modal name="delete-tag-{{ $tag->id }}" class="min-w-[22rem]">
                                            <form wire:submit="delete({{ $tag->id }})" class="space-y-6">
                                                <div class="space-y-4!">
                                                    <flux:heading size="lg" class="font-semibold -mt-1.5!">
                                                        Delete Tag?
                                                    </flux:heading>

                                                    <flux:subheading>
                                                        Are you sure you want to delete the

                                                        <span class="font-semibold text-red-500">
                                                            '{{ $tag->name }}'
                                                        </span>
        
                                                        tag?
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
        </div>
    </x-headerless-card>
</div>