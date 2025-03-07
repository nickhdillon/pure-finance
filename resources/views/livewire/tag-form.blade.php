<div>
    <flux:modal name="{{ $tag ? ('edit-tag-' . $tag->id) : 'add-tag' }}">
        <form wire:submit='submit' class="space-y-6">
            <flux:heading size="lg" class="font-semibold -mt-1.5!">
                {{ ($tag ? 'Edit' : 'Create') . ' Tag' }}
            </flux:heading>

            <flux:field>
                <flux:label>Name</flux:label>

                <flux:input type="text" wire:model='name' required />

                <flux:error name="name" />
            </flux:field>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost" size="sm">
                        Cancel
                    </flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="primary" size="sm">
                    Save
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
