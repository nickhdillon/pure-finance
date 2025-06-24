@use('App\Enums\BillAlert', 'BillAlert')
@use('App\Enums\RecurringFrequency', 'RecurringFrequency')

<div x-on:set-default-date.window="$wire.date = $event.detail.date">
    <flux:modal wire:model.self="show_bill_form" name="bill-form" variant="flyout" class="w-[325px]!" x-on:close="$wire.resetForm()">
        <div class="space-y-6 relative">
            <flux:heading size="lg">
                {{ ($bill ? 'Edit' : 'Create') . ' Bill' }}
            </flux:heading>

            <form class="space-y-6">
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

                <flux:field>
                    <flux:label>Name</flux:label>

                    <flux:input type="text" wire:model='name' />

                    <flux:error name="name" />
                </flux:field>

                <x-categories :$categories />

                <flux:field class="w-full">
                    <flux:label>Amount</flux:label>

                    <flux:input type="number" inputmode="decimal" wire:model='amount' placeholder="100.00" step="0.01" required />

                    <flux:error name="amount" />
                </flux:field>

                <flux:field>
                    <flux:label>Date</flux:label>

                    <flux:date-picker wire:model='date' clearable with-today />

                    <flux:error name="date" />
                </flux:field>

                <flux:field>
                    <flux:label>Frequency</flux:label>

                    <flux:select variant="listbox" placeholder="Select a frequency..." clearable wire:model='frequency'>
                        @foreach (RecurringFrequency::cases() as $frequency)
                            <flux:select.option value="{{ $frequency->value }}">
                                Every {{ $frequency->label() }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:error name="frequency" />
                </flux:field>

                <flux:field>
                    <flux:label>Notes</flux:label>

                    <flux:textarea wire:model='notes' rows="5" resize="none" />

                    <flux:error name="notes" />
                </flux:field>

                <flux:field>
                    <flux:label>Attachments</flux:label>

                    <livewire:file-uploader :files="$bill?->attachments" 
                        :wire:key="'file-uploader-' . ($bill?->id ?? 'new')" flyout="true" />
                </flux:field>

                <flux:field>
                    <flux:label>Alerts</flux:label>

                    <div class="flex flex-col gap-1.5 w-full min-w-0">
                        <div class="rounded-[8px] shadow-xs border border-zinc-200 bg-white dark:bg-white/10 md:flex min-w-0 w-full divide-y md:divide-x md:divide-y-0 dark:border-white/10 divide-zinc-200 dark:divide-white/10">
                            <div class="md:!w-[60%] w-full min-w-0">
                                <flux:select variant="listbox" placeholder="First alert..." clearable class="borderless-select"
                                    wire:model='first_alert'
                                >
                                    @foreach (BillAlert::cases() as $alert)
                                        <flux:select.option value="{{ $alert->value }}">
                                            {{ $alert->label() }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                            </div>

                            <div class="md:!w-[40%] w-full min-w-0">
                                <flux:select variant="listbox" placeholder="At" clearable class="borderless-select"
                                    wire:model='first_alert_time'
                                >
                                    @foreach ($times as $time)
                                        <flux:select.option value="{{ $time }}">
                                            {{ $time }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                            </div>
                        </div>

                        <div class="rounded-[8px] shadow-xs border dark:border-white/10 border-zinc-200 bg-white dark:bg-white/10 md:flex min-w-0 w-full divide-y md:divide-x md:divide-y-0 divide-zinc-200 dark:divide-white/10">
                            <div class="md:w-[60%] w-full">
                                <flux:select variant="listbox" placeholder="Second alert..." clearable class="borderless-select"
                                    wire:model='second_alert'
                                >
                                    @foreach (BillAlert::cases() as $alert)
                                        <flux:select.option value="{{ $alert->value }}">
                                            {{ $alert->label() }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                            </div>

                            <div class="md:w-[40%] w-full">
                                <flux:select variant="listbox" placeholder="At" clearable class="borderless-select"
                                    wire:model='second_alert_time'
                                >
                                    @foreach ($times as $time)
                                        <flux:select.option value="{{ $time }}">
                                            {{ $time }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                            </div>
                        </div>
                    </div>

                    <flux:error name="first_alert" />
                    <flux:error name="first_alert_time" />
                    <flux:error name="second_alert" />
                    <flux:error name="second_alert_time" />
                </flux:field>

                <div class="flex">
                    <flux:spacer />

                    <div class="flex flex-col gap-2 w-full">
                        @if ($bill && $bill->children()->count()) 
                            <flux:modal.trigger name="save-bill" class="w-full">
                                <flux:button variant="primary">Save</flux:button>
                            </flux:modal.trigger>

                            <flux:modal name="save-bill">
                                <flux:heading size="lg">Save Bill</flux:heading>

                                <flux:text class="mt-2">
                                    Would you like to save these changes for just this bill, or all instances of this bill?
                                </flux:text>

                                <div class="flex mt-4">
                                    <flux:spacer />

                                    <div class="flex items-center gap-1.5">
                                        <flux:button wire:click='submit' x-on:click="$dispatch('bill-submitted')" variant="primary" size="sm">
                                            This bill only
                                        </flux:button>

                                        <flux:button wire:click='submit(true)' x-on:click="$dispatch('bill-submitted')" variant="outline" size="sm" class="text-emerald-500!">
                                            All instances
                                        </flux:button>
                                    </div>
                                </div>
                            </flux:modal>
                        @else
                            <flux:button wire:click='submit' variant="primary" class="w-full">
                                Save
                            </flux:button>
                        @endif

                        @if ($bill)
                            <div class="flex items-center gap-2 w-full">
                                <flux:button wire:click='changePaidStatus' variant="outline" class="w-full">
                                    {{ $paid ? 'Mark as unpaid' : 'Mark as paid' }}
                                </flux:button>

                                <div class="w-full">
                                    <flux:modal.trigger name="delete-bill">
                                        <flux:button variant="outline" class="w-full text-rose-500! font-medium!">
                                            Delete
                                        </flux:button>
                                    </flux:modal.trigger>

                                    <flux:modal name="delete-bill">
                                        <flux:heading size="lg">Delete Bill</flux:heading>
                                        
                                        @if ($bill->children()->count())
                                            <flux:text class="mt-2">
                                                Would you like to delete just this bill, or all instances of this bill?
                                            </flux:text>

                                            <div class="flex mt-4">
                                                <flux:spacer />

                                                <div class="flex items-center gap-1.5">
                                                    <flux:button wire:click='delete' x-on:click="$dispatch('bill-submitted')" variant="danger" size="sm">
                                                        This bill only
                                                    </flux:button>

                                                    <flux:button wire:click="delete(true)" x-on:click="$dispatch('bill-submitted')" variant="outline" size="sm" class="text-rose-500!">
                                                        All instances
                                                    </flux:button>
                                                </div>
                                            </div>
                                        @else
                                            <flux:text class="mt-2">
                                                Are you sure you want to delete this bill?
                                            </flux:text>

                                            <div class="flex mt-4">
                                                <flux:spacer />

                                                <flux:button wire:click="delete" variant="danger" size="sm">
                                                    Yes, delete
                                                </flux:button>
                                            </div>
                                        @endif
                                    </flux:modal>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
