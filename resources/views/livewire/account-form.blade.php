@use('App\Enums\AccountType', 'AccountType')

<div>
    <flux:modal.trigger name="{{ $account ? ('edit-account' . $account->id) : 'add-account' }}">
        <flux:button icon="plus" variant="primary" size="sm" class="h-7!">
            Add
        </flux:button>
    </flux:modal.trigger>

    <flux:modal name="{{ $account ? ('edit-account' . $account->id) : 'add-account' }}">
        <form wire:submit='submit' class="space-y-6">
            <flux:heading size="lg" class="font-semibold -mt-1.5!">
                Create Account
            </flux:heading>

            <flux:field>
                <flux:label>Name</flux:label>

                <flux:input type="text" wire:model='name' required />

                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>Account Type</flux:label>

                <flux:select variant="listbox" placeholder="Select account type" wire:model='type' clearable required>
                    @foreach (AccountType::cases() as $account_type)
                        <flux:select.option value="{{ $account_type->value }}">
                            {{ $account_type->label() }}
                        </flux:select.option>
                    @endforeach
                </flux:select>

                <flux:error name="account_type" />
            </flux:field>

            <flux:field>
                <flux:label>Initial Balance</flux:label>

                <flux:input type="number" wire:model='initial_balance' required />

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
