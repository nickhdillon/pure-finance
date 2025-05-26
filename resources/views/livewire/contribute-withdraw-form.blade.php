<div x-data="{ 
    header: '',
    setHeader(e) { this.header = e.detail.header }
}" x-on:open-form.window="setHeader">
    <flux:modal name="contribute-withdraw-form">
        <form wire:submit='submit' class="space-y-6">
            <flux:heading size="lg" class="font-semibold -mt-1.5!">
                <span x-text="header"></span>
            </flux:heading>

            <template x-if="header.includes('Contribute')">
                <flux:field>
                    <flux:label>Amount to contribute</flux:label>

                    <flux:input type="number" inputmode="decimal" wire:model='contribution_amount' placeholder="100.00" step="0.01" required />

                    <flux:description class="-mt-0.5!">
                        Suggested monthly contribution: 
                        ${{ Number::format($savings_goal->monthly_contribution ?? 0, 2) }}
                    </flux:description>

                    <flux:error name="contribution_amount" />
                </flux:field>
            </template>

            <template x-if="header.includes('Withdraw')">
                <flux:field>
                    <flux:label>Amount to withdraw</flux:label>

                    <flux:input type="number" inputmode="decimal" wire:model='withdrawal_amount' placeholder="100.00" step="0.01" required />

                    <flux:error name="withdrawal_amount" />
                </flux:field>
            </template>

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
        </form>
    </flux:modal>
</div>
