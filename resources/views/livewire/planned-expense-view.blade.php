<div class="space-y-4">
    <flux:heading size="xl">
        Planned Expense
    </flux:heading>

    <x-card heading="{{ $expense->name }}">
        <x-slot:button>
            <div>
                <flux:modal.trigger name="{{ 'edit-expense' . $expense->id }}">
                    <flux:button icon="pencil-square" variant="primary" size="sm">
                        Edit
                    </flux:button>
                </flux:modal.trigger>

                <livewire:planned-spending-form :$expense />
            </div>
        </x-slot:button>

        <x-slot:content>
            <div>
                <h3 class="px-4 pt-3 text-sm font-medium">
                    Spending in category: {{ $expense->category->name }}
                </h3>

                <div class="flex flex-col justify-between">
                    <div class="flex flex-col w-full">
                        <div class="flex flex-col px-4 py-3 space-y-2 text-sm">
                            <h3 class="font-medium uppercase">
                                This Month
                            </h3>

                            <div>
                                <p>Available</p>

                                <div class="flex items-center justify-between">
                                    <h3 @class([
                                        '!text-red-500' => $percentage_spent > 100,
                                        'font-semibold',
                                    ])>
                                        @if ($percentage_spent <= 100)
                                            ${{ Number::format($available ?? 0, 2) }}
                                        @else
                                            ${{ Number::format(abs($available) ?? 0, 2) }} over spent
                                        @endif
                                    </h3>

                                    <p>
                                        {{ $transaction_count }} {{ Str::plural('transaction', $transaction_count) }}
                                    </p>
                                </div>

                                <div class="w-full my-1.5 h-8 sm:h-9 bg-zinc-50 dark:bg-zinc-700 shadow-sm rounded-lg">
                                    <div @class([
                                        '!rounded-r-lg' => $percentage_spent >= 100,
                                        '!bg-red-500' => $percentage_spent > 100,
                                        'min-w-[25px]' => $percentage_spent > 0,
                                        '!bg-transparent' => $percentage_spent === 0,
                                        'flex items-center justify-center h-full bg-emerald-500 rounded-lg rounded-r-none',
                                    ])
                                        style="width: {{ min($percentage_spent, 100) }}%;">
                                        <span x-cloak x-show="$wire.percentage_spent > 0"
                                            class="font-semibold text-white">
                                            {{ Number::format($percentage_spent, 0) }}%
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <p>
                                        Spent

                                        <span class="font-semibold">
                                            ${{ Number::format($total_spent ?? 0, 2) }}
                                        </span>
                                    </p>

                                    <p>
                                        of

                                        <span class="font-semibold">
                                            ${{ Number::format($expense->monthly_amount ?? 0, 2) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($has_non_zero_spending)
                        <div class="w-full px-4 py-3">
                            <h3 class="mb-2 text-sm font-medium uppercase">
                                Last 6 Months
                            </h3>

                            <flux:chart wire:model="monthly_totals" class="aspect-2/1 sm:aspect-3/1">
                                <flux:chart.svg>
                                    <flux:chart.line field="total_spent" class="text-emerald-500 dark:text-emerald-400" curve="none" />
                                    <flux:chart.area field="total_spent" class="text-emerald-200/50 dark:text-emerald-400/30" curve="none" />

                                    <flux:chart.axis axis="y" position="left" tick-prefix="$" :format="[
                                        'notation' => 'compact',
                                        'compactDisplay' => 'short',
                                        'maximumFractionDigits' => 1,
                                    ]">
                                        <flux:chart.axis.grid />
                                        <flux:chart.axis.tick />
                                    </flux:chart.axis>

                                    <flux:chart.axis axis="x" field="month">
                                        <flux:chart.axis.tick />
                                        <flux:chart.axis.line />
                                    </flux:chart.axis>

                                    <flux:chart.zero-line />
                                </flux:chart.svg>

                                <flux:chart.tooltip>
                                    <flux:chart.tooltip.heading field="month" />
                                    <flux:chart.tooltip.value field="total_spent" label="Total Spent" :format="[
                                        'style' => 'currency', 
                                        'currency' => 'USD',
                                        'notation' => 'compact'
                                        ]"
                                    />
                                </flux:chart.tooltip>
                            </flux:chart>
                        </div>
                    @endif
                </div>
            </div>
        </x-slot:content>
    </x-card>
</div>
