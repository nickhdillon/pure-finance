<div>
    <flux:modal wire:model.self='show_tag_form' name="{{ $tag ? ('edit-tag-' . $tag->id) : 'add-tag' }}">
        <div class="space-y-6">
            <div class="space-y-6">
                <flux:heading size="lg" class="font-semibold -mt-1.5!">
                    {{ ($tag ? 'Edit' : 'Create') . ' Tag' }}
                </flux:heading>

                <flux:field>
                    <flux:label>Name</flux:label>

                    <flux:input type="text" wire:model='name' />

                    <flux:error name="name" />
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
    </flux:modal>
</div>
