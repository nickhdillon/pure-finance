@use('App\Enums\TransactionType', 'TransactionType')

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

                <div class="flex flex-col sm:flex-row justify-between">
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
                                    <flux:modal.trigger name="expense-transactions">
                                        <button x-on:click="$dispatch('load-transactions')"
                                        @class([
                                            '!rounded-r-lg' => $percentage_spent >= 100,
                                            '!bg-red-500 hover:!bg-red-400' => $percentage_spent > 100,
                                            'min-w-[25px]' => $percentage_spent > 0,
                                            '!bg-transparent' => $percentage_spent === 0,
                                            'flex items-center justify-center h-full bg-emerald-500 hover:bg-emerald-400 rounded-lg cursor-pointer rounded-r-none duration-200 ease-in-out',
                                        ])
                                            style="width: {{ min($percentage_spent, 100) }}%;">
                                            <span x-cloak x-show="$wire.percentage_spent > 0"
                                                class="font-semibold text-white">
                                                {{ Number::format($percentage_spent, 0) }}%
                                            </span>
                                        </button>
                                    </flux:modal.trigger>
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

                    <div class="w-full px-4 py-3">
                        <h3 class="mb-2 sm:pl-8 text-sm font-medium uppercase">
                            Last 6 Months
                        </h3>

                        <div wire:ignore class="relative sm:items-center flex flex-col my-8 text-sm">
                            <div class="flex items-end mb-2">
                                @foreach ($monthly_totals->reverse() as $month)
                                    <flux:modal.trigger name="expense-transactions" wire:key="{{ $month['total_spent'] }}">
                                        <button x-on:click="$dispatch('load-transactions', { month: '{{ $month['month'] }}' })"
                                            class="w-[40px] mx-2 sm:mx-3 cursor-pointer">
                                            <div style="height: {{ $month['total_spent'] }}px"
                                                class="relative bg-emerald-500 hover:bg-emerald-400 duration-200 ease-in-out shadow-sm max-h-[250px] rounded-t-lg">
                                                <div class="absolute top-0 left-0 right-0 -mt-6 text-sm text-center">
                                                    ${{ $month['total_spent'] }}
                                                </div>
                                            </div>
                                        </button>
                                    </flux:modal.trigger>
                                @endforeach
                            </div>

                            <div class="flex items-end">
                                @foreach ($monthly_totals->reverse() as $month)
                                    <div class="w-[40px] mx-2 sm:mx-3" wire:key="{{ $month['month'] }}">
                                        <div class="relative">
                                            <div class="absolute top-0 left-0 right-0 mt-1 text-sm text-center">
                                                {{ $month['month'] }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <flux:modal name="expense-transactions" x-on:close="$wire.resetTransactions()">
                        <div wire:loading.remove class="space-y-4 text-sm">
                            <flux:heading size="lg" class="font-semibold -mt-1!">
                                Transactions for {{ $selected_month }}
                            </flux:heading>

                            <div class="divide-y divide-zinc-200 dark:divide-zinc-600">
                                @if ($transactions) 
                                    @foreach ($transactions as $transaction)
                                        <div class="first:pt-0 py-2 last:pb-0">
                                            <div class="flex items-center justify-between font-medium">
                                                <flux:button
                                                    href="{{ route('edit-transaction', $transaction) }}"
                                                    variant="ghost"
                                                    class="text-emerald-500! hover:text-emerald-600! dark:hover:text-emerald-400! hover:bg-transparent! p-0! h-4!"
                                                >
                                                    {{ $transaction->payee }}
                                                </flux:button>
                                                                            
                                                <div class="flex items-center">
                                                    @if (in_array($transaction->type, [TransactionType::DEBIT, TransactionType::TRANSFER, TransactionType::WITHDRAWAL]))
                                                        <span class="text-zinc-700 dark:text-zinc-200">-</span>
                                                    @else
                                                        <span class="text-emerald-500">+</span>
                                                    @endif
                                
                                                    <span @class([
                                                        '!text-emerald-500' => in_array($transaction->type, [
                                                            TransactionType::CREDIT,
                                                            TransactionType::DEPOSIT,
                                                        ]),
                                                        'text-zinc-700 dark:text-zinc-200'
                                                    ])>
                                                        ${{ Number::format($transaction->amount ?? 0, 2) }}
                                                    </span>
                                                </div>
                                            </div>
                                    
                                            <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-300">
                                                <div class="flex items-center">
                                                    <p class="max-w-[200px] truncate sm:max-w-full">
                                                        @if ($transaction->category->parent)
                                                            {{ $transaction->category->parent->name }} &rarr; {{ $transaction->category->name }}
                                                        @else
                                                            {{ $transaction->category->name }}
                                                        @endif
                                                    </p>
                                                </div>
                                    
                                                <p>
                                                    {{ Carbon\Carbon::parse($transaction->date)->format('M j, Y') }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div x-cloak wire:loading.flex class="flex items-center justify-center w-full h-[253px]">
                            <flux:icon.loading />
                        </div>
                    </flux:modal>
                </div>
            </div>
        </x-slot:content>
    </x-card>
</div>
