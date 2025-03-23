@props(['account' => null, 'accounts', 'categories'])

@use('App\Enums\TransactionType', 'TransactionType')

<div x-data="{
    showStatus: true,
    showTypes: true,
    showAccounts: true,
    showCategories: true,
    showDates: true,
    slideOverOpen: false,
    totalFilters() {
        total = $wire.selected_accounts.length + $wire.selected_categories.length;

        if ($wire.status || $wire.status === false) total++;

        if ($wire.transaction_type) total++;

        if ($wire.date) total++;

        return total;
    },
}" class="relative z-20 w-auto h-auto">
    <flux:modal.trigger name="filters">
        <div class="relative inline-block">
            <flux:button icon="funnel" variant="ghost" x-on:click="slideOverOpen = true" />

            <span x-cloak x-show="totalFilters() > 0"
                class="absolute top-1 right-1 flex items-center justify-center w-[19px] h-[19px] -mt-2 -mr-2 text-xs bg-emerald-600 rounded-full text-slate-200"
                x-text="totalFilters()">
            </span>
        </div>
    </flux:modal.trigger>

    <flux:modal name="filters" variant="flyout" class="space-y-6 px-5! w-[250px]!">
        <div class="flex mt-4 items-center justify-between">
            <flux:heading size="lg">
                Filters
            </flux:heading>

            <div class="flex items-center space-x-1">
                <flux:button variant="ghost" x-cloak x-show="totalFilters() > 1"
                    class="w-18! h-6!"
                    x-on:click="$dispatch('clear-filters')">
                    Clear all
                </flux:button>
            </div>
        </div>

        <div class="relative flex-1 space-y-3">
            <div>
                <div class="flex pb-0.5 items-center justify-between text-sm font-medium">
                    <p>
                        Status
                    </p>

                    <div class="flex items-center justify-between">
                        <flux:button variant="ghost" x-cloak x-show="$wire.status || $wire.status === false"
                            class="w-14! h-6!"
                            x-on:click="$wire.set('status', null)">
                            Clear
                        </flux:button>

                        <flux:button variant="subtle" icon="chevron-down"
                            class="!h-6 !w-6 !-mr-0.5"
                            x-bind:class="showStatus ? 'rotate-180' : ''" x-on:click="showStatus = !showStatus" />
                    </div>
                </div>

                <div x-collapse x-show="showStatus" class="pt-2 border-t border-slate-300 dark:border-slate-700">
                    <flux:radio.group wire:model.boolean.live="status" class="space-y-2.5!">
                        <flux:radio value="true" label="Cleared" />

                        <flux:radio value="false" label="Pending" />
                    </flux:radio.group>
                </div>
            </div>

            <div>
                <div class="flex pb-0.5 items-center justify-between text-sm font-medium">
                    <p>
                        Types
                    </p>

                    <div class="flex items-center justify-between">
                        <flux:button variant="ghost" x-cloak x-show="$wire.transaction_type"
                            class="w-14! h-6!"
                            x-on:click="$wire.set('transaction_type', '')">
                            Clear
                        </flux:button>

                        <flux:button variant="subtle" icon="chevron-down"
                            class="!h-6 !w-6 !-mr-0.5"
                            x-bind:class="showTypes ? 'rotate-180' : ''" x-on:click="showTypes = !showTypes" />
                    </div>
                </div>

                <div x-collapse x-show="showTypes" class="pt-2 border-t border-slate-300 dark:border-slate-700">
                    <flux:radio.group wire:model.live="transaction_type" class="space-y-2.5!">
                        @foreach (TransactionType::cases() as $type)
                            <flux:radio value="{{ $type }}" label="{{ $type->label() }}" />
                        @endforeach
                    </flux:radio.group>
                </div>
            </div>

            @if (!$account)
                <div>
                    <div class="flex pb-0.5 items-center justify-between text-sm font-medium">
                        <p>
                            Accounts
                        </p>

                        <div class="flex items-center justify-between">
                            <flux:button variant="ghost" x-cloak x-show="$wire.selected_accounts.length > 0"
                                class="w-14! h-6!"
                                x-on:click="$wire.set('selected_accounts', [])">
                                Clear
                            </flux:button>

                            <flux:button variant="subtle" icon="chevron-down"
                                class="!h-6 !w-6 !-mr-0.5"
                                x-bind:class="showAccounts ? 'rotate-180' : ''" x-on:click="showAccounts = !showAccounts" />
                        </div>
                    </div>

                    <div x-collapse x-show="showAccounts" class="pt-2 border-t space-y-2.5 border-slate-300 dark:border-slate-700">
                        @foreach ($accounts as $account)
                            <flux:checkbox wire:model.live="selected_accounts" label="{{ $account }}"
                                value="{{ $account }}" />
                        @endforeach
                    </div>
                </div>
            @endif

            <div>
                <div class="pb-0.5 text-sm font-medium flex items-center justify-between">
                    <p>
                        Categories
                    </p>

                    <div class="flex items-center justify-between">
                        <flux:button variant="ghost" x-cloak x-show="$wire.selected_categories.length > 0"
                                class="w-14! h-6!"
                                x-on:click="$wire.set('selected_categories', [])">
                                Clear
                            </flux:button>

                            <flux:button variant="subtle" icon="chevron-down"
                                class="!h-6 !w-6 !-mr-0.5"
                                x-bind:class="showCategories ? 'rotate-180' : ''" x-on:click="showCategories = !showCategories" />
                    </div>
                </div>

                <div x-collapse x-show="showCategories"
                    class="pt-2 border-t space-y-2.5 border-slate-300 dark:border-slate-700">
                    @foreach ($categories as $category)
                        <flux:checkbox wire:model.live="selected_categories" label="{{ $category['name'] }}"
                            value="{{ $category['name'] }}" />
                    @endforeach
                </div>
            </div>

            <div>
                <div class="flex pb-0.5 items-center justify-between text-sm font-medium">
                    <p>
                        Dates
                    </p>

                    <div class="flex items-center justify-between">
                        <flux:button variant="ghost" x-cloak x-show="$wire.date"
                            class="w-14! h-6!"
                            x-on:click="$wire.set('date', '')">
                            Clear
                        </flux:button>

                        <flux:button variant="subtle" icon="chevron-down"
                            class="!h-6 !w-6 !-mr-0.5"
                            x-bind:class="showDates ? 'rotate-180' : ''" x-on:click="showDates = !showDates" />
                    </div>
                </div>

                <div x-collapse x-show="showDates" class="pt-2 border-t border-slate-300 dark:border-slate-700 space-y-2">
                    <flux:radio.group wire:model.live="date" class="space-y-2.5!">
                        <flux:radio value="{{ now()->subDays(7) }}" label="Last 7 Days" />

                        <flux:radio value="{{ now()->subDays(30) }}" label="Last 30 Days" />

                        <flux:radio value="{{ now()->subMonths(3) }}" label="Last 3 Months" />

                        <flux:radio value="{{ now()->subMonths(6) }}" label="Last 6 Months" />
                    </flux:radio.group>
                </div>
            </div>
        </div>
    </flux:modal>
</div>
