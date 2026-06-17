@use('App\Enums\PlannedExpenseType', 'PlannedExpenseType')

<div>
    <flux:modal name="add-expense">
        <form wire:submit='submit' class="space-y-6">
            <flux:heading size="lg" class="font-semibold -mt-1.5!">
                Create Expense
            </flux:heading>

            <flux:field>
                <flux:label>Name</flux:label>

                <flux:input type="text" wire:model='name' required />

                <flux:error name="name" />
            </flux:field>

            <x-categories :$categories />

            <flux:field>
                <flux:label>Monthly Amount</flux:label>

                <flux:input type="number" inputmode="decimal" wire:model='monthly_amount' placeholder="100.00" step="0.01" required />

                <flux:error name="monthly_amount" />
            </flux:field>

            <flux:field>
                <flux:radio.group wire:model="type" label="Type">
                    @foreach (PlannedExpenseType::cases() as $type)
                        <flux:radio :value="$type->value" :label="$type->label()" />
                    @endforeach
                </flux:radio.group>
            </flux:field>

            <div class="flex gap-2 items-center">
                <div class="ml-auto flex gap-2">
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
