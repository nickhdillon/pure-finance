<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-stretch lg:justify-between">
        <div>
            <flux:heading size="xl">Monthly Overview</flux:heading>
            <flux:text class="mt-0.5">{{ $this->start->format('F Y') }}</flux:text>
        </div>

        <flux:button.group>
            <flux:button wire:click="previousMonth" class="h-7! sm:h-8! px-1.5! sm:px-2!" variant="outline" size="sm">
                <flux:icon.chevron-left icon-variant="outline" class="h-[14px] w-[14px] stroke-2" />
            </flux:button>

            <flux:button size="sm" wire:click="currentMonth" class="h-7! sm:h-8! px-2! sm:px-4!">
                <span class="hidden sm:block">Today</span>

                <flux:icon.calendar icon-variant="outline" class="sm:hidden h-4 w-4 stroke-2" />
            </flux:button>

            <flux:button wire:click="nextMonth" class="h-7! sm:h-8! px-1.5! sm:px-2!" variant="outline" size="sm">
                <flux:icon.chevron-right icon-variant="outline" class="h-[14px] w-[14px] stroke-2" />
            </flux:button>
        </flux:button.group>
    </div>

    <x-card heading="Bills">
        <x-slot:content>
            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($this->bills['rows'] as $row)
                    <div class="flex items-center justify-between p-3 text-sm duration-200 ease-in-out first:rounded-t-[8px] last:rounded-b-[8px] font-medium">
                        <div class="flex flex-col">
                            <p>{{ $row['name'] }}</p>

                            <flux:text size="sm">{{ $row['date'] }}</flux:text>
                        </div>
    
                        <div class="flex items-center gap-2">
                            @if ($row['paid']) 
                                <flux:badge color="emerald" class="h-6!" size="sm">Paid</flux:badge>
                            @else
                                <flux:badge color="amber" class="h-6!" size="sm">Unpaid</flux:badge>
                            @endif

                            <p>{{ Number::currency($row['amount']) }}</p>
                        </div>
                    </div>
                @empty
                    <flux:text class="p-3 text-center">No bills due this month.</flux:text>
                @endforelse
            </div>

            @if ($this->bills['rows']->isNotEmpty())
                <div class="flex items-center justify-end gap-5 border-t border-zinc-200 px-3 py-2.5 dark:border-zinc-700 bg-zinc-100/50 dark:bg-zinc-800 text-sm w-full">
                    <p class="font-medium">
                        Paid:

                        <span class="text-emerald-500">{{ Number::currency($this->bills['paid_total']) }}</span>
                    </p>

                    <p class="font-medium">
                        Unpaid:

                        <span class="text-amber-500">{{ Number::currency($this->bills['unpaid_total']) }}</span>
                    </p>

                    <p class="font-medium">Total: {{ Number::currency($this->bills['total']) }}</p>
                </div>
            @endif
        </x-slot:content>
    </x-card>

    {{-- <div class="grid gap-4 xl:grid-cols-2">
        <flux:card class="overflow-hidden p-0!">
            <div class="flex items-center justify-between border-b border-zinc-200 px-4 py-4 dark:border-zinc-700">
                <div class="flex items-center gap-2.5">
                    <div class="flex size-8 items-center justify-center rounded-lg bg-orange-500/10 text-orange-500"><flux:icon.wallet class="size-4" /></div>
                    <div><flux:heading size="lg">Planned Spending</flux:heading><flux:text class="mt-0.5 text-xs">Grouped by parent category</flux:text></div>
                </div>
                <flux:button :href="route('planned-spending')" wire:navigate size="sm" variant="ghost" icon:trailing="chevron-right">View all</flux:button>
            </div>

            <div class="overflow-x-auto">
                <flux:table class="min-w-[38rem]">
                    <flux:table.columns class="bg-zinc-50 dark:bg-zinc-800">
                        <flux:table.column>Category</flux:table.column><flux:table.column align="end">Spent</flux:table.column><flux:table.column align="end">Planned</flux:table.column><flux:table.column align="end">Left</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($planned_spending as $row)
                            <flux:table.row :key="$row['id']" wire:click="openTransactions('spending-group', {{ $row['id'] }}, '{{ addslashes($row['name']) }}')" class="cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                <flux:table.cell variant="strong"><span class="flex items-center gap-2"><span class="flex size-6 items-center justify-center rounded-md bg-orange-500/10 text-orange-500"><flux:icon.wallet class="size-3.5" /></span>{{ $row['name'] }}</span></flux:table.cell>
                                <flux:table.cell align="end">{{ Number::currency($row['spent'], 'USD') }}</flux:table.cell><flux:table.cell align="end">{{ Number::currency($row['planned'], 'USD') }}</flux:table.cell><flux:table.cell align="end" @class(['text-emerald-500!' => $row['left'] >= 0, 'text-red-500!' => $row['left'] < 0])>{{ Number::currency($row['left'], 'USD') }}</flux:table.cell>
                            </flux:table.row>
                        @endforeach
                        <flux:table.row wire:click="openTransactions('unplanned-spending', null, 'Unplanned spending')" class="cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                            <flux:table.cell variant="strong"><span class="flex items-center gap-2"><flux:badge size="sm" color="red" variant="pill">Unplanned</flux:badge></span></flux:table.cell><flux:table.cell align="end">{{ Number::currency($unplanned_spending, 'USD') }}</flux:table.cell><flux:table.cell align="end">—</flux:table.cell><flux:table.cell align="end" class="text-red-500!">{{ Number::currency(-$unplanned_spending, 'USD') }}</flux:table.cell>
                        </flux:table.row>
                        <flux:table.row wire:click="openTransactions('spending', null, 'All spending')" class="cursor-pointer bg-zinc-50 font-medium hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-700">
                            <flux:table.cell variant="strong">Total</flux:table.cell><flux:table.cell align="end">{{ Number::currency($spent_total, 'USD') }}</flux:table.cell><flux:table.cell align="end">{{ Number::currency($planned_spending_total, 'USD') }}</flux:table.cell><flux:table.cell align="end" @class(['text-emerald-500!' => $planned_spending_total - $spent_total >= 0, 'text-red-500!' => $planned_spending_total - $spent_total < 0])>{{ Number::currency($planned_spending_total - $spent_total, 'USD') }}</flux:table.cell>
                        </flux:table.row>
                    </flux:table.rows>
                </flux:table>

                @if ($planned_spending->isEmpty())
                    <flux:text class="px-4 py-6 text-center">No spending plans for this month.</flux:text>
                @endif
            </div>
        </flux:card>

        <flux:card class="overflow-hidden p-0!">
            <div class="flex items-center justify-between border-b border-zinc-200 px-4 py-4 dark:border-zinc-700">
                <div class="flex items-center gap-2.5"><div class="flex size-8 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-500"><flux:icon.banknotes class="size-4" /></div><div><flux:heading size="lg">Income</flux:heading><flux:text class="mt-0.5 text-xs">Expected and unplanned sources for the month</flux:text></div></div>
                <flux:modal.trigger name="add-income"><flux:button icon="plus" size="sm" variant="primary">Add income</flux:button></flux:modal.trigger>
            </div>

            <div class="overflow-x-auto">
                <flux:table class="min-w-[38rem]">
                    <flux:table.columns class="bg-zinc-50 dark:bg-zinc-800">
                        <flux:table.column>Income source</flux:table.column><flux:table.column align="end">Received</flux:table.column><flux:table.column align="end">Planned</flux:table.column><flux:table.column align="end">Left</flux:table.column><flux:table.column></flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($income_rows as $row)
                            <flux:table.row :key="$row['id']" wire:click="openTransactions('income-item', {{ $row['id'] }}, '{{ addslashes($row['name']) }}')" class="cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                <flux:table.cell variant="strong">
                                    <span class="flex items-center gap-2">
                                        <span class="flex size-6 items-center justify-center rounded-md bg-emerald-500/10 text-emerald-500"><flux:icon.banknotes class="size-3.5" /></span>
                                        {{ $row['name'] }}
                                        <flux:badge size="sm" color="emerald" variant="pill">Expected</flux:badge>
                                    </span>
                                </flux:table.cell>
                                <flux:table.cell align="end">{{ Number::currency($row['received'], 'USD') }}</flux:table.cell><flux:table.cell align="end">{{ Number::currency($row['planned'], 'USD') }}</flux:table.cell><flux:table.cell align="end" @class(['text-emerald-500!' => $row['left'] >= 0, 'text-red-500!' => $row['left'] < 0])>{{ Number::currency($row['left'], 'USD') }}</flux:table.cell>
                                <flux:table.cell align="end"><flux:button wire:click="deleteIncome({{ $row['id'] }})" x-on:click.stop wire:confirm="Remove this income?" icon="x-mark" size="sm" variant="ghost" square aria-label="Remove {{ $row['name'] }}" /></flux:table.cell>
                            </flux:table.row>
                        @endforeach
                        <flux:table.row wire:click="openTransactions('extra-income', null, 'Other and extra income')" class="cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                            <flux:table.cell variant="strong"><span class="flex items-center gap-2"><flux:badge size="sm" color="amber" variant="pill">Unplanned</flux:badge></span></flux:table.cell><flux:table.cell align="end">{{ Number::currency($extra_income, 'USD') }}</flux:table.cell><flux:table.cell align="end">—</flux:table.cell><flux:table.cell align="end" class="text-emerald-500!">{{ Number::currency($extra_income, 'USD') }}</flux:table.cell><flux:table.cell></flux:table.cell>
                        </flux:table.row>
                        <flux:table.row wire:click="openTransactions('income', null, 'All income')" class="cursor-pointer bg-zinc-50 font-medium hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-700">
                            <flux:table.cell variant="strong">Total</flux:table.cell><flux:table.cell align="end">{{ Number::currency($received_total, 'USD') }}</flux:table.cell><flux:table.cell align="end">{{ Number::currency($planned_income_total, 'USD') }}</flux:table.cell><flux:table.cell align="end" class="text-emerald-500!">{{ Number::currency($expected_income - $received_total, 'USD') }}</flux:table.cell><flux:table.cell></flux:table.cell>
                        </flux:table.row>
                    </flux:table.rows>
                </flux:table>

                @if ($income_rows->isEmpty())
                    <flux:text class="px-4 py-6 text-center">No income logged yet. Add a source to get started.</flux:text>
                @endif
            </div>
        </flux:card>
    </div>

    <flux:card class="p-5!">
        <div class="flex items-center gap-2.5"><div class="flex size-8 items-center justify-center rounded-lg bg-sky-500/10 text-sky-500"><flux:icon.chart-no-axes-combined class="size-4" /></div><div><flux:heading size="lg">Monthly Summary</flux:heading><flux:text class="mt-0.5 text-xs">Plan versus current expectation</flux:text></div></div>
        <div class="mt-5 grid gap-px overflow-hidden rounded-lg bg-zinc-200 dark:bg-zinc-700 sm:grid-cols-2 xl:grid-cols-4">
            @php
                $summary = [
                    ['label' => 'Planned spending', 'value' => $planned_spending_total, 'scope' => 'spending'], ['label' => 'Expected spending', 'value' => $expected_spending, 'scope' => 'spending'],
                    ['label' => 'Planned income', 'value' => $planned_income_total, 'scope' => 'income'], ['label' => 'Expected income', 'value' => $expected_income, 'scope' => 'income'],
                    ['label' => 'Planned savings', 'value' => $planned_savings, 'scope' => 'all'], ['label' => 'Expected savings', 'value' => $expected_savings, 'scope' => 'all'],
                    ['label' => 'Savings difference', 'value' => $savings_difference, 'scope' => 'all'], ['label' => 'Starting net worth', 'value' => $starting_net_worth, 'scope' => 'all'],
                    ['label' => 'Current net worth', 'value' => $current_net_worth, 'scope' => 'all'], ['label' => 'Planned end-of-month net worth', 'value' => $planned_end_net_worth, 'scope' => 'all'],
                    ['label' => 'Expected end-of-month net worth', 'value' => $expected_end_net_worth, 'scope' => 'all'],
                ];
            @endphp
            @foreach ($summary as $item)
                <button type="button" wire:click="openTransactions('{{ $item['scope'] }}', null, '{{ $item['label'] }}')" class="bg-white p-4 text-left transition hover:bg-emerald-50 dark:bg-zinc-800 dark:hover:bg-emerald-950/20">
                    <flux:text size="xs">{{ $item['label'] }}</flux:text>
                    <flux:heading size="lg" @class(['mt-2 tabular-nums', 'text-red-500!' => $item['value'] < 0, 'text-emerald-500!' => $item['label'] === 'Savings difference' && $item['value'] >= 0])>{{ Number::currency($item['value'], 'USD') }}</flux:heading>
                </button>
            @endforeach
        </div>
    </flux:card>

    <flux:text class="text-center text-xs">Amounts include transactions dated through {{ $current_date->format('F j, Y') }}. Select any row to see its contributing transactions.</flux:text>

    <flux:modal name="add-income" class="md:w-[30rem]">
        <form wire:submit="saveIncome" class="space-y-5">
            <div><flux:heading size="lg">Add income</flux:heading><flux:text class="mt-1">Log an income for {{ $start->format('F Y') }}.</flux:text></div>
            <flux:input wire:model="income_name" size="sm" label="Income name" placeholder="Paycheck" required />
            <flux:radio.group wire:model="income_type" variant="segmented" size="sm" label="Type">
                <flux:radio value="expected" label="Expected" />
                <flux:radio value="unplanned" label="Unplanned" />
            </flux:radio.group>
            <flux:date-picker wire:model="income_date" size="sm" label="Date" min="{{ $start->toDateString() }}" max="{{ $end->toDateString() }}" required />
            <flux:input wire:model="income_amount" size="sm" icon="currency-dollar" type="number" min="0.01" step="0.01" label="Amount" placeholder="0.00" required />
            <div class="flex justify-end gap-2"><flux:modal.close><flux:button size="sm" variant="ghost">Cancel</flux:button></flux:modal.close><flux:button size="sm" type="submit" variant="primary">Save income</flux:button></div>
        </form>
    </flux:modal>

    <flux:modal wire:model.self="show_transactions" name="overview-transactions" class="md:w-[56rem]">
        <div class="space-y-5">
            <div><flux:heading size="lg">{{ $transaction_modal_title }}</flux:heading><flux:text class="mt-1">{{ $start->format('F Y') }}</flux:text></div>
            <div class="max-h-[60vh] overflow-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
                <flux:table class="min-w-[42rem]">
                    <flux:table.columns class="sticky top-0 bg-zinc-100 dark:bg-zinc-800">
                        <flux:table.column>Date</flux:table.column>
                        <flux:table.column>Payee</flux:table.column>
                        <flux:table.column>Account</flux:table.column>
                        <flux:table.column>Category</flux:table.column>
                        <flux:table.column align="end">Amount</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($modal_transactions as $transaction)
                            <flux:table.row :key="$transaction['id']">
                                <flux:table.cell class="whitespace-nowrap">{{ $transaction['date'] }}</flux:table.cell>
                                <flux:table.cell variant="strong">{{ $transaction['payee'] }}</flux:table.cell>
                                <flux:table.cell>{{ $transaction['account'] }}</flux:table.cell>
                                <flux:table.cell><flux:badge size="sm">{{ $transaction['category'] }}</flux:badge></flux:table.cell>
                                <flux:table.cell align="end" @class(['tabular-nums', 'text-emerald-500!' => $transaction['income']])>{{ Number::currency($transaction['amount'], 'USD') }}</flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>

                @if ($modal_transactions === [])
                    <flux:text class="p-10 text-center">No transactions contribute to this amount yet.</flux:text>
                @endif
            </div>
            <div class="flex justify-end"><flux:modal.close><flux:button size="sm">Close</flux:button></flux:modal.close></div>
        </div>
    </flux:modal> --}}
</div>
