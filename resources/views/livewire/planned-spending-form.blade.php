<div>
    <flux:modal name="{{ $expense ? ('edit-expense' . $expense->id) : 'add-expense' }}">
        <form wire:submit='submit' class="space-y-6">
            <flux:heading size="lg" class="font-semibold -mt-1.5!">
                {{ $expense ? 'Edit' : 'Create' }} Expense
            </flux:heading>

            <flux:field>
                <flux:label>Name</flux:label>

                <flux:input type="text" wire:model='name' required />

                <flux:error name="name" />
            </flux:field>

            <x-categories :$categories />

            <flux:field>
                <flux:label>Monthly Amount</flux:label>

                <flux:input type="number" wire:model='monthly_amount' placeholder="100.00" step="0.01" required />

                <flux:error name="monthly_amount" />
            </flux:field>

            <div class="flex gap-2 items-center justify-between">
                <div>
                    <flux:modal.trigger name="delete-expense">
                        <flux:button variant="danger" size="sm">
                            Delete
                        </flux:button>
                    </flux:modal.trigger>

                    <x-delete-modal name="delete-expense" heading="expense" />
                </div>

                <div>
                    <flux:modal.close>
                        <flux:button variant="ghost" size="sm">
                            Cancel
                        </flux:button>
                    </flux:modal.close>

                    <flux:button type="submit" variant="primary" size="sm">
                        Save
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
</div>
