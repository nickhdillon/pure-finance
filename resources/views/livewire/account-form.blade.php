@use('App\Enums\AccountType', 'AccountType')

<div>
    <flux:modal name="{{ $account ? ('edit-account' . $account->id) : 'add-account' }}">
        <form wire:submit='submit' class="space-y-6">
            <flux:heading size="lg" class="font-semibold -mt-1.5!">
                {{ $account ? 'Edit' : 'Create' }} Account
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

                <flux:input type="number" inputmode="decimal" wire:model='initial_balance' placeholder="100.00" step="0.01" :disabled="$account" required />

                <flux:error name="name" />
            </flux:field>

            <div class="flex gap-2 items-center">
                @if ($account) 
                    <div>
                        <flux:modal.trigger name="delete-account">
                            <flux:button variant="danger" size="sm">
                                Delete
                            </flux:button>
                        </flux:modal.trigger>
            
                        <x-delete-modal name="delete-account" heading="account" />
                    </div>
                @endif
            
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
