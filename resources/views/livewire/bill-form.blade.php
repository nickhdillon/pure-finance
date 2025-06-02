@use('App\Enums\BillColor', 'BillColor')
@use('App\Enums\RecurringFrequency', 'RecurringFrequency')

<div>
    <flux:modal name="bill-form" variant="flyout" class="w-[325px]!" x-on:close="$wire.resetForm()">
        <div wire:loading.remove class="space-y-6 relative">
            <flux:heading size="lg">
                {{ ($bill ? 'Edit' : 'Create') . ' Bill' }}
            </flux:heading>

            <form class="space-y-6">
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
                    <flux:label>Notes</flux:label>

                    <flux:textarea wire:model='notes' rows="5" resize="none" />

                    <flux:error name="notes" />
                </flux:field>

                <flux:field>
                    <flux:label>Color</flux:label>

                    <flux:select variant="listbox" placeholder="Select a color..." clearable wire:model='color'>
                        @foreach (BillColor::cases() as $color)
                            <flux:select.option value="{{ $color->value }}">
                                <div class="flex items-center gap-1.5">
                                    <p class="size-4 rounded-full {{ $color->labelColor() }}" />
                                    <p>{{ $color->label() }}</p>
                                </div>
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:error name="color" />
                </flux:field>

                @if ($bill) 
                    <flux:field>
                        <flux:label>Status</flux:label>
    
                        <div class="flex items-center gap-1.5">
                            <flux:switch wire:model='paid' class="bg-amber-500! data-checked:bg-emerald-500!" />
    
                            <button type="button" class="text-sm text-zinc-500 dark:text-zinc-400 italic"
                                x-text="$wire.paid ? 'Paid' : 'Unpaid'"
                                x-on:click="$wire.paid = !$wire.paid"
                            />
                        </div>
    
                        <flux:error name="paid" />
                    </flux:field>
                @endif

                <div class="flex">
                    <flux:spacer />

                    <flux:button wire:click='submit' variant="primary" class="w-full">
                        Save
                    </flux:button>
                </div>
            </form>
        </div>

        <div x-cloak wire:loading.flex class="absolute inset-0 flex justify-center items-center">
            <flux:icon.loading />
        </div>        
    </flux:modal>
</div>
