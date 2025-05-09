<div>
    <flux:modal wire:model.self='show_category_form' name="category-form" x-on:close="$wire.resetForm()">
        <div wire:loading.remove class="space-y-6">
            <div class="space-y-6">
                <flux:heading size="lg" class="font-semibold -mt-1.5!">
                    {{ ($category ? 'Edit' : 'Create') . ' Category' }}
                </flux:heading>

                <flux:field>
                    <flux:label>Name</flux:label>

                    <flux:input type="text" wire:model='name' />

                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Parent</flux:label>

                    <flux:select variant="listbox" placeholder="Select a parent category" wire:model='parent_id' clearable>
                        @foreach ($this->parent_categories as $parent)
                            <flux:select.option value="{{ $parent->id }}">
                                {{ $parent->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:error name="parent_id" />
                </flux:field>

                <div class="flex gap-2">
                    <flux:spacer />

                    <flux:modal.close>
                        <flux:button variant="ghost" size="sm">
                            Cancel
                        </flux:button>
                    </flux:modal.close>

                    <flux:button type="button" wire:click='submit' variant="primary" size="sm">
                        Save
                    </flux:button>
                </div>
            </div>
        </div>

        <div x-cloak wire:loading.flex class="flex items-center justify-center w-full h-[253px]">
            <flux:icon.loading />
        </div>
    </flux:modal>
</div>
