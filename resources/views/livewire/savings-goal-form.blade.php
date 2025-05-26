<div x-data="savingsGoalForm" class="space-y-4 max-w-4xl mx-auto">
    <flux:heading size="xl">
        {{ $savings_goal ? 'Edit' : 'New' }} Goal
    </flux:heading>

    <form wire:submit='submit' class="space-y-6">
        <x-card>
            <x-slot:content>
                <div class="p-5 space-y-5">
                    <flux:field class="w-full">
                        <flux:label>Account</flux:label>

                        <flux:select variant="listbox" placeholder="Select an account"
                            wire:model='account_id' clearable required>
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

                        <flux:input type="text" wire:model='name' required />

                        <flux:error name="name" />
                    </flux:field>

                    <div class="flex items-center justify-between flex-col sm:flex-row gap-5">
                        <flux:field class="w-full">
                            <flux:label>Goal Amount</flux:label>

                            <flux:input type="number" inputmode="decimal" x-model='goalAmount' placeholder="100.00" step="0.01" required />

                            <flux:error name="goal_amount" />
                        </flux:field>

                        <flux:field class="w-full">
                            <flux:label>Amount Saved</flux:label>

                            <flux:input type="number" inputmode="decimal" x-model='amountSaved' placeholder="100.00" step="0.01" />

                            <flux:error name="amount_saved" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <div class="flex items-center flex-wrap gap-1.5">
                            <flux:label>Monthly Contribution</flux:label>

                            <p class="text-xs italic text-zinc-500 dark:text-zinc-400">
                                (Automatically recalculated if selecting a target month/year)
                            </p>
                        </div>

                        <flux:input type="number" inputmode="decimal" x-model='monthlyContribution' placeholder="100.00" step="0.01" required />

                        <flux:error name="monthly_contribution" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Target</flux:label>

                        <div class="flex items-center gap-1.5">
                            <flux:switch wire:model='target' class="bg-amber-500! data-checked:bg-emerald-500!" />

                            <button type="button" class="text-sm text-zinc-500 dark:text-zinc-400 italic"
                                x-text="$wire.target ? 'Yes' : 'No'"
                                x-on:click="$wire.target = !$wire.target"
                            />
                        </div>

                        <flux:error name="target" />
                    </flux:field>

                    <div x-cloak x-show="$wire.target" x-collapse
                        class="flex items-center justify-between flex-col sm:flex-row gap-5">
                        <flux:field class="w-full">
                            <flux:label>Target Month</flux:label>

                            <flux:select variant="listbox" placeholder="Select a month" x-model='targetMonth' clearable>
                                @foreach ($months as $month)
                                    <flux:select.option value="{{ $month }}">
                                        {{ $month }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>

                            <flux:error name="target_month" />
                        </flux:field>

                        <flux:field class="w-full">
                            <flux:label>Target Year</flux:label>

                            <flux:select variant="listbox" placeholder="Select a year" x-model='targetYear' clearable>
                                @foreach ($years as $year)
                                    <flux:select.option value="{{ $year }}">
                                        {{ $year }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>

                            <flux:error name="target_year" />
                        </flux:field>
                    </div>

                    <div class="flex gap-2 items-center">
                        @if ($savings_goal) 
                            <div>
                                <flux:modal.trigger name="delete-goal">
                                    <flux:button variant="danger" size="sm">
                                        Delete
                                    </flux:button>
                                </flux:modal.trigger>
                    
                                <x-delete-modal name="delete-goal" heading="goal" />
                            </div>
                        @endif
                    
                        <div class="ml-auto flex gap-2">
                            <flux:button href="{{ $savings_goal ? route('savings-goal-view', $savings_goal) : route('dashboard') }}"
                                wire:navigate variant="outline" class="!px-4" size="sm">
                                Cancel
                            </flux:button>
                    
                            <flux:button variant="primary" class="!px-4" size="sm" type="submit">
                                Submit
                            </flux:button>
                        </div>
                    </div>
                </div>
            </x-slot:content>
        </x-card>
    </form>
</div>

@script
    <script>
        Alpine.data('savingsGoalForm', () => {
            return {
                goalAmount: $wire.entangle('goal_amount'),
                amountSaved: $wire.entangle('amount_saved'),
                target: $wire.entangle('target'),
                targetMonth: $wire.entangle('target_month'),
                targetYear: $wire.entangle('target_year'),
                monthlyContribution: $wire.entangle('monthly_contribution'),

                init() {
                    ['goalAmount', 'amountSaved', 'target', 'targetMonth', 'targetYear'].forEach(prop => {
                        this.$watch(prop, () => this.updateMonthlyContribution());
                    });

                    this.updateMonthlyContribution();
                },

                updateMonthlyContribution() {
                    if (!this.target) return;
                    
                    const goal = parseFloat(this.goalAmount) || 0;
                    const saved = parseFloat(this.amountSaved) || 0;
                    const remaining = goal - saved;
                    const monthsLeft = this.getRemainingMonths();

                    this.monthlyContribution = monthsLeft > 0
                        ? (remaining / monthsLeft).toFixed(2)
                        : remaining > 0 ? remaining.toFixed(2) : 0;
                },

                getRemainingMonths() {
                    if (!this.targetMonth || !this.targetYear) return 0;

                    const monthIndex = new Date(`${this.targetMonth} 1, ${this.targetYear}`).getMonth();
                    const target = new Date(this.targetYear, monthIndex);
                    const now = new Date();
                    target.setDate(1);
                    now.setDate(1);

                    const months =
                        (target.getFullYear() - now.getFullYear()) * 12 +
                        (target.getMonth() - now.getMonth());

                    return months;
                }
            };
        });
    </script>
@endscript