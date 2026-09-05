@use('App\Enums\IncomeType', 'IncomeType')

<div>
    <flux:modal
        wire:model.self="show_income_form"
        name="income-form"
        flyout variant="floating"
        class="w-[360px]!"
        x-on:close="$wire.resetForm()"
    >
        <div class="space-y-6">
            <flux:heading size="lg">{{ $income ? 'Edit' : 'Add' }} income</flux:heading>

            <form wire:submit="submit" class="space-y-5">
                <flux:field>
                    <flux:label>Source</flux:label>

                    <flux:input wire:model="name" placeholder="Paycheck, bonus, freelance work..." />

                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Amount</flux:label>

                    <flux:input
                        wire:model="amount"
                        type="number"
                        inputmode="decimal"
                        step="0.01"
                        min="0.01"
                        placeholder="0.00"
                    />

                    <flux:error name="amount" />
                </flux:field>

                <flux:field>
                    <flux:label>Income type</flux:label>

                    <flux:select wire:model="type" variant="listbox">
                        @foreach (IncomeType::cases() as $income_type)
                            <flux:select.option value="{{ $income_type->value }}">
                                {{ $income_type->label() }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:error name="type" />
                </flux:field>

                <flux:field>
                    <flux:label>
                        {{ $type === IncomeType::EXPECTED ? 'Expected on' : 'Received on' }}
                    </flux:label>

                    <flux:date-picker wire:model="date" with-today />

                    <flux:error name="date" />
                </flux:field>

                <flux:field>
                    <flux:label>
                        Notes <span class="ml-1 font-normal text-zinc-500">(optional)</span>
                    </flux:label>

                    <flux:textarea wire:model="notes" rows="3" resize="none" />

                    <flux:error name="notes" />
                </flux:field>

                @if (! $income)
                    <flux:field variant="inline">
                        <flux:checkbox wire:model="received" />

                        <flux:label>Already received</flux:label>

                        <flux:description class="-mt-1!">I already created the transaction for this income.</flux:description>

                        <flux:error name="received" />
                    </flux:field>
                @endif

                <div class="flex justify-end gap-2">
                    <flux:modal.close>
                        <flux:button variant="ghost" size="sm">Cancel</flux:button>
                    </flux:modal.close>

                    <flux:button type="submit" variant="primary" size="sm">Save</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
