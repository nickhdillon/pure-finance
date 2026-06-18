@use('App\Enums\PlannedExpenseType', 'PlannedExpenseType')

<div x-data="{ showApplyToFutureMonths: false }">
    <flux:modal name="{{ 'edit-expense' . $expense_month->id }}">
        <form wire:submit='submit' class="space-y-6">
            <flux:heading size="lg" class="font-semibold -mt-1.5!">
                Edit Expense
            </flux:heading>

            <flux:field>
                <flux:label>Name</flux:label>

                <flux:input type="text" wire:model='name' required />

                <flux:error name="name" />
            </flux:field>

            <x-categories :$categories />

            <flux:field>
                <flux:label>Amount</flux:label>

                <flux:input
                    type="number"
                    inputmode="decimal"
                    x-model="$wire.amount"
                    x-on:input="showApplyToFutureMonths = true"
                    placeholder="100.00"
                    step="0.01"
                    required
                />

                <flux:error name="amount" />
            </flux:field>

            <flux:field>
                <flux:radio.group wire:model="type" label="Type">
                    @foreach (PlannedExpenseType::cases() as $type)
                        <flux:radio :value="$type->value" :label="$type->label()" />
                    @endforeach
                </flux:radio.group>
            </flux:field>

            <div x-cloak x-show="showApplyToFutureMonths">
                <flux:field variant="inline">
                    <flux:checkbox wire:model='apply_to_future_months' />

                    <flux:label>Apply to future months</flux:label>

                    <flux:error name="apply_to_future_months" />
                </flux:field>
            </div>

            <div class="flex gap-2 items-center">
                <div>
                    <flux:modal.trigger name="delete-expense">
                        <flux:button variant="danger" size="sm">
                            Delete
                        </flux:button>
                    </flux:modal.trigger>
        
                    <x-delete-modal name="delete-expense" heading="expense" />
                </div>
            
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
