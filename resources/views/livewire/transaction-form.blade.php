@use('App\Enums\TransactionType', 'TransactionType')
@use('App\Enums\RecurringFrequency', 'RecurringFrequency')

<div class="space-y-4 max-w-4xl mx-auto" x-data="{ type: $wire.entangle('type').live }">
    <flux:heading size="xl">
        {{ $transaction ? 'Edit' : 'New' }} Transaction
    </flux:heading>

    <fieldset @disabled($type === TransactionType::CREDIT && $transfer_to)>
        <form wire:submit='submit' class="space-y-5">
            <x-card>
                <x-slot:content>
                    <div class="p-5 grid items-start grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="space-y-5">
                            @if (!$account && !$transaction)
                                <flux:field>
                                    <flux:label>Account</flux:label>

                                    <flux:select variant="listbox" placeholder="Select an account" wire:model.blur='account_id' clearable>
                                        @foreach ($accounts as $account)
                                            <flux:select.option value="{{ $account->id }}">
                                                {{ $account->name }}
                                            </flux:select.option>
                                        @endforeach
                                    </flux:select>

                                    <flux:error name="account_id" />
                                </flux:field>
                            @endif

                            <flux:field>
                                <flux:label>Type</flux:label>

                                <flux:select variant="listbox" placeholder="Select a type" x-model='type' clearable>
                                    @foreach ($transaction_types as $transaction_type)
                                        <flux:select.option value="{{ $transaction_type->value }}">
                                            {{ $transaction_type->label() }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>

                                <flux:error name="type" />
                            </flux:field>

                            <template x-cloak x-if="type === 'transfer'">
                                <flux:field>
                                    <flux:label>Transfer To</flux:label>

                                    <flux:select variant="listbox" placeholder="Select an account" wire:model.live='transfer_to' clearable>
                                        @foreach ($accounts as $account)
                                            @if ($account->id !== $account_id)
                                                <flux:select.option value="{{ $account->id }}">
                                                    {{ $account->name }}
                                                </flux:select.option>
                                            @endif
                                        @endforeach
                                    </flux:select>

                                    <flux:error name="transfer_to" />
                                </flux:field>
                            </template>

                            <x-categories :$categories :disabled="$type === TransactionType::CREDIT && $transfer_to" />

                            <x-tags :$user_tags :disabled="$type === TransactionType::CREDIT && $transfer_to" />
                        </div>

                        <div class="space-y-5">
                            <flux:field>
                                <flux:label>Payee</flux:label>

                                <flux:input type="text" wire:model='payee' required :disabled="isset($transfer_to)" />

                                <flux:error name="payee" />
                            </flux:field>

                            <flux:field>
                                <flux:label
                                    @class([
                                        'opacity-50' => $type === TransactionType::CREDIT && $transfer_to
                                    ])>
                                    Amount
                                </flux:label>

                                <flux:input icon="currency-dollar" icon-variant="outline" type="text" inputmode="decimal" placeholder="100.00" step="0.01" wire:model='amount' required />

                                <flux:error name="amount" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Date</flux:label>

                                <flux:date-picker wire:model='date' clearable with-today />

                                <flux:error name="date" />
                            </flux:field>

                            <div class="flex items-center gap-4 justify-between w-full sm:w-[90%] md:w-[80%] lg:w-[70%] xl:w-[60%]">
                                <flux:field>
                                    <flux:label>Status</flux:label>

                                    <div class="flex items-center gap-1.5">
                                        <flux:switch wire:model='status' class="bg-amber-500! data-checked:bg-emerald-500!" />

                                        <button type="button" class="text-sm text-zinc-500 dark:text-zinc-400 italic"
                                            x-text="$wire.status ? 'Cleared' : 'Pending'"
                                            x-on:click="$wire.status = !$wire.status"
                                        />
                                    </div>

                                    <flux:error name="status" />
                                </flux:field>

                                <flux:field>
                                    <flux:label>Mark as recurring</flux:label>

                                    <div class="flex items-center gap-1.5">
                                        <flux:switch wire:model='is_recurring' class="bg-amber-500! data-checked:bg-emerald-500!" />

                                        <button type="button" class="text-sm text-zinc-500 dark:text-zinc-400 italic"
                                            x-text="$wire.is_recurring ? 'Yes' : 'No'"
                                            x-on:click="$wire.is_recurring = !$wire.is_recurring"
                                        />
                                    </div>

                                    <flux:error name="is_recurring" />
                                </flux:field>
                            </div>
                        </div>

                        <flux:field class="sm:col-span-2">
                            <flux:label
                                @class([
                                    'opacity-50' => $type === TransactionType::CREDIT && $transfer_to
                                ])>
                                Notes
                            </flux:label>

                            <flux:textarea wire:model='notes' rows="5" resize="none" />

                            <flux:error name="notes" />
                        </flux:field>
                    </div>
                </x-slot:content>
            </x-card>

            <div x-cloak x-show="$wire.is_recurring" x-collapse>
                <x-card heading="Recurring Details">
                    <x-slot:content>
                        <div class="p-5 gap-5 grid grid-cols-1 sm:grid-cols-2 items-center">
                            <flux:field>
                                <flux:label>Frequency</flux:label>

                                <flux:select variant="listbox" searchable placeholder="Select a frequency..." clearable wire:model='frequency'>
                                    @foreach (RecurringFrequency::cases() as $frequency)
                                        <flux:select.option value="{{ $frequency->value }}">
                                            Every {{ $frequency->label() }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>

                                <flux:error name="frequency" />
                            </flux:field>

                            <flux:field>
                                <flux:label>End Date</flux:label>

                                <flux:date-picker wire:model='recurring_end' clearable with-today selectable-header />

                                <flux:error name="recurring_end" />
                            </flux:field>
                        </div>
                    </x-slot:content>
                </x-card>
            </div>
            
            <div x-cloak>
                <x-card heading="Attachments" class="border-none bg-transparent! inset-shadow-lg!">                 
                    <x-slot:content>
                        <livewire:file-uploader :files="$transaction?->attachments"
                            :disabled="($type === TransactionType::CREDIT) && $transfer_to" />
                    </x-slot:content>
                </x-card>
            </div>
                
            <div class="flex justify-between col-start-2">
                @if ($transaction)
                    <div>
                        <flux:modal.trigger name="delete-transaction-{{ $transaction->id }}">
                            <flux:button variant="danger" size="sm">
                                Delete
                            </flux:button>
                        </flux:modal.trigger>

                        <flux:modal name="delete-transaction-{{ $transaction->id }}" class="min-w-[22rem]">
                            <div class="space-y-6 text-left">
                                <div class="space-y-4!">
                                    <flux:heading size="lg" class="font-semibold -mt-1.5!">
                                        Delete Transaction?
                                    </flux:heading>

                                    <flux:subheading>
                                        Are you sure you want to delete this transaction?
                                    </flux:subheading>
                                </div>

                                <div class="flex gap-2">
                                    <flux:spacer />

                                    <flux:modal.close>
                                        <flux:button variant="ghost" size="sm">
                                            Cancel
                                        </flux:button>
                                    </flux:modal.close>

                                    <flux:button type="button" wire:click="delete({{ $transaction->id }})" variant="danger" size="sm">
                                        Confirm
                                    </flux:button>
                                </div>
                            </div>
                        </flux:modal>
                    </div>
                @endif

                <div class="space-x-1 ml-auto text-sm text-white">
                    <flux:button href="{{ route('dashboard') }}" wire:navigate variant="outline" class="!px-4" size="sm">
                        Cancel
                    </flux:button>
                    
                    <flux:button variant="primary" class="!px-4" size="sm" type="submit">
                        Submit
                    </flux:button>
                </div>
            </div>
        </form>
    </fieldset>
</div>