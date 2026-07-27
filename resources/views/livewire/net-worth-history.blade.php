<x-card heading="Net Worth">
    <x-slot:button>
        <flux:button
            wire:click="goToToday"
            size="sm"
            variant="ghost"
            aria-label="{{ __('Jump to today') }}"
        >
            Today
        </flux:button>
    </x-slot:button>

    <x-slot:content>
        <div class="flex flex-col gap-6 p-4 sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Net worth as of {{ $selected_date_label }}
                    </p>

                    <p class="mt-1 text-3xl font-semibold tracking-tight tabular-nums text-zinc-900 dark:text-white">
                        {{ Number::currency($net_worth, 'USD') }}
                    </p>

                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        Includes scheduled transactions through this date.
                    </p>
                </div>

                <div class="flex items-end gap-2">
                    <flux:date-picker
                        label="Point in time"
                        wire:model.live="selected_date"
                        max="9999-12-31"
                        clearable
                    />

                    <flux:button
                        wire:click="previousDay"
                        icon="chevron-left"
                        aria-label="{{ __('Show previous day') }}"
                        class="h-[38px]"
                    />

                    <flux:button
                        wire:click="nextDay"
                        icon="chevron-right"
                        aria-label="{{ __('Show next day') }}"
                        class="h-[38px]"
                    />
                </div>
            </div>

            <div class="border-t border-zinc-200 pt-5 dark:border-zinc-700">
                <p class="text-sm font-medium text-zinc-900 dark:text-white">6-month trend</p>
                <p class="mb-4 text-xs text-zinc-500 dark:text-zinc-400">
                    The six months leading up to your selected date.
                </p>

                <flux:chart :value="$chart_data->all()" class="h-64 sm:h-80">
                    <flux:chart.svg>
                        <flux:chart.area
                            field="net_worth"
                            class="text-emerald-200/50 dark:text-emerald-400/20"
                        />

                        <flux:chart.line
                            field="net_worth"
                            class="text-emerald-600 dark:text-emerald-400"
                        />

                        <flux:chart.axis
                            axis="x"
                            field="axis_label"
                            scale="categorical"
                        >
                            <flux:chart.axis.line />
                            <flux:chart.axis.mark />
                            <flux:chart.axis.tick />
                        </flux:chart.axis>

                        <flux:chart.axis
                            axis="y"
                            position="left"
                            :format="[
                                'style' => 'currency',
                                'currency' => 'USD',
                                'notation' => 'compact',
                                'maximumFractionDigits' => 1,
                            ]"
                        >
                            <flux:chart.axis.grid class="text-zinc-200/70 dark:text-zinc-700/70" />

                            <flux:chart.axis.tick />
                        </flux:chart.axis>

                        <flux:chart.zero-line class="text-zinc-400 dark:text-zinc-500" />

                        <flux:chart.cursor />
                    </flux:chart.svg>

                    <flux:chart.tooltip>
                        <flux:chart.tooltip.heading
                            field="date"
                            :format="['year' => 'numeric', 'month' => 'long', 'day' => 'numeric']"
                        />

                        <flux:chart.tooltip.value
                            field="net_worth"
                            label="Net worth"
                            :format="['style' => 'currency', 'currency' => 'USD']"
                        />
                    </flux:chart.tooltip>
                </flux:chart>
            </div>
        </div>
    </x-slot:content>
</x-card>
