<div>
    <x-card heading="Income">
        <x-slot:button>
            <flux:modal.trigger name="income-form">
                <flux:button icon="plus" variant="primary" size="sm">
                    Add
                </flux:button>
            </flux:modal.trigger>
        </x-slot:button>
    
        <x-slot:content>
            @php
                $incomeCards = $this->incomeCards;
                $showGroups = count($incomeCards) > 1;
            @endphp

            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($incomeCards as $income_group)
                    <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @if ($showGroups)
                            <p
                                @class([
                                    'text-emerald-600 dark:text-emerald-400' => $income_group['name'] === 'Expected',
                                    'text-amber-500 dark:text-amber-500' => $income_group['name'] === 'Unplanned',
                                    'text-sm px-3 py-2.5 font-semibold uppercase'
                                ])
                            >
                                {{ $income_group['name'] }}
                            </p>
                        @endif

                        <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @forelse ($income_group['incomes'] as $income)
                                <div class="flex items-center justify-between gap-3 p-3">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <p class="truncate text-sm font-medium">{{ $income->name }}</p>

                                            @if ($income->received)
                                                <flux:badge size="sm" color="emerald">Received</flux:badge>
                                            @endif
                                        </div>

                                        <p class="mt-0.5 truncate text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ $income->date->format('M j, Y') }}

                                            @if ($income->notes)
                                                <span aria-hidden="true">·</span> {{ $income->notes }}
                                            @endif
                                        </p>
                                    </div>

                                    <div class="flex shrink-0 items-center gap-1.5">
                                        <flux:dropdown position="bottom" align="end">
                                            <flux:button
                                                icon="ellipsis-horizontal"
                                                variant="ghost"
                                                size="sm"
                                                inset="top bottom"
                                                aria-label="Income actions"
                                            />

                                            <flux:menu>
                                                @if (! $income->received)
                                                    <flux:modal.trigger
                                                        name="mark-income-received"
                                                        wire:click="confirmReceipt({{ $income->id }})"
                                                    >
                                                        <flux:menu.item icon="check-circle">Mark received</flux:menu.item>
                                                    </flux:modal.trigger>
                                                @endif

                                                <flux:modal.trigger
                                                    name="income-form"
                                                    x-on:click="$dispatch('load-income', { income_id: {{ $income->id }} })"
                                                >
                                                    <flux:menu.item icon="pencil">Edit</flux:menu.item>
                                                </flux:modal.trigger>

                                                <flux:modal.trigger name="delete-income" wire:click="confirmDelete({{ $income->id }})">
                                                    <flux:menu.item icon="trash" class="text-red-600! dark:text-red-400!">
                                                        Delete
                                                    </flux:menu.item>
                                                </flux:modal.trigger>
                                            </flux:menu>
                                        </flux:dropdown>

                                        <p class="text-sm font-medium text-emerald-600 dark:text-emerald-400">
                                            +${{ Number::format($income->amount, 2) }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <p class="p-5 text-center text-xs text-zinc-500 dark:text-zinc-400">
                                    No {{ Str::lower($income_group['name']) }} yet.
                                </p>
                            @endforelse
                        </div>

                        @if ($showGroups)
                            <div class="flex w-full items-center justify-between gap-2 bg-zinc-100/50 px-3 py-2.5 text-sm dark:bg-zinc-800">
                                <p class="font-medium">Total {{ $income_group['name'] }}: </p>

                                <p class="font-medium"> ${{ Number::format($income_group['total'], 2) }}</p>
                            </div>
                        @endif
                    </div>
                @empty
                    <div
                        class="p-2.5 text-sm italic font-medium text-center text-zinc-800 whitespace-nowrap dark:text-zinc-200">
                        No income found...
                    </div>
                @endforelse

                <div class="flex items-center font-medium justify-between space-x-1 py-2.5 px-3 text-sm w-full">
                    <p>Total Income:</p>

                    <p>${{ Number::format($this->incomeTotal ?? 0, 2) }}</p>
                </div>
            </div>
        </x-slot:content>
    </x-card>

    <flux:modal name="mark-income-received" class="w-full max-w-md">
        <flux:heading size="lg">
            Mark income as received?
        </flux:heading>

        <flux:text class="mt-2">
            This will create a deposit transaction for this income and update your account balance.
        </flux:text>

        <div class="mt-5 space-y-4">
            <flux:field>
                <flux:label>Deposit to</flux:label>

                <flux:select
                    wire:model="receiving_account_id"
                    variant="listbox"
                    placeholder="Select an account"
                >
                    @foreach ($accounts as $account)
                        <flux:select.option value="{{ $account->id }}">
                            {{ $account->name }}
                        </flux:select.option>
                    @endforeach
                </flux:select>

                <flux:error name="receiving_account_id" />
            </flux:field>

            <x-categories :$categories />
        </div>

        <div class="mt-6 flex justify-end gap-2">
            <flux:modal.close>
                <flux:button variant="ghost" size="sm">Cancel</flux:button>
            </flux:modal.close>

            <flux:button variant="primary" size="sm" wire:click="markReceived">
                Create transaction
            </flux:button>
        </div>
    </flux:modal>

    <flux:modal name="delete-income">
        <flux:heading size="lg">Delete income?</flux:heading>

        <flux:text class="mt-2">This will permanently remove this income entry.</flux:text>

        <div class="mt-4 flex justify-end gap-2">
            <flux:modal.close>
                <flux:button variant="ghost" size="sm">Cancel</flux:button>
            </flux:modal.close>

            <flux:button
                variant="danger"
                size="sm"
                wire:click="delete({{ $deleting_income_id }})"
            >
                Delete
            </flux:button>
        </div>
    </flux:modal>

    @livewire('income-form')
</div>
