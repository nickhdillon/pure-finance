@use('App\Enums\TransactionType', 'TransactionType')

<div x-on:account-saved.window="$wire.$refresh" class="space-y-4">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">
            Transactions
        </flux:heading>

        @if (auth()->user()->accounts()->count() > 0)
            <flux:button
                href="{{ $account ? route('account.transaction-form', $account) : route('create-transaction') }}"
                wire:navigate variant="primary" icon="plus" size="sm">
                Add
            </flux:button>
        @endif
    </div>

    <x-card>
        <x-slot:content>                
            <div class="p-3 gap-2.5 flex items-center justify-between dark:bg-zinc-900 rounded-t-[8px]">
                <flux:input icon="magnifying-glass" placeholder="Search transactions..." wire:model.live.debounce.300ms='search' clearable />

                <div class="flex items-center">
                    <x-filters :$account :$accounts :$categories />

                    <x-columns :$columns />
                </div>
            </div>

            @if ($transactions->count() > 0)
                <flux:table :paginate="$transactions" class="border-t border-zinc-200 dark:border-white/20">
                    <flux:table.columns class="[&>tr>th]:!px-3 hidden sm:table-header-group bg-zinc-50 dark:bg-zinc-800">
                        @if (in_array('date', $columns))
                            <flux:table.column sortable :sorted="$sort_col === 'date'" :direction="$sort_direction"
                            wire:click="sortBy('date')">
                                Date
                            </flux:table.column>
                        @endif

                        @if (in_array('payee', $columns))
                            <flux:table.column sortable :sorted="$sort_col === 'payee'" :direction="$sort_direction"
                            wire:click="sortBy('payee')">
                                Payee
                            </flux:table.column>
                        @endif

                        @if (!$account && in_array('account', $columns))
                            <flux:table.column sortable :sorted="$sort_col === 'account'" :direction="$sort_direction"
                            wire:click="sortBy('account')">
                                Account
                            </flux:table.column>
                        @endif

                        @if (in_array('category', $columns))
                            <flux:table.column sortable :sorted="$sort_col === 'category'" :direction="$sort_direction"
                            wire:click="sortBy('category')">
                                Category
                            </flux:table.column>
                        @endif

                        @if (in_array('type', $columns))
                            <flux:table.column sortable :sorted="$sort_col === 'type'" :direction="$sort_direction"
                            wire:click="sortBy('type')">
                                Type
                            </flux:table.column>
                        @endif

                        @if (in_array('amount', $columns))
                            <flux:table.column sortable :sorted="$sort_col === 'amount'" :direction="$sort_direction"
                            wire:click="sortBy('amount')">
                                Amount
                            </flux:table.column>
                        @endif

                        @if (in_array('status', $columns))
                            <flux:table.column sortable :sorted="$sort_col === 'status'" :direction="$sort_direction"
                            wire:click="sortBy('status')">
                                Status
                            </flux:table.column>
                        @endif

                        <flux:table.column align="end">
                            Actions
                        </flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows class="sm:hidden dark:bg-zinc-900">
                        @foreach ($transactions as $transaction)
                            <flux:table.row :key="$transaction->id">
                                <flux:table.cell class="!p-0">
                                    <x-transaction :$transaction />
                                </flux:table.cell>
                            </flux:table.row>                        
                        @endforeach
                    </flux:table.rows>

                    <flux:table.rows class="hidden sm:table-row-group dark:bg-zinc-900">
                        @foreach ($transactions as $transaction)
                            <flux:table.row :key="$transaction->id" class="[&>td]:px-3!">
                                @if (in_array('date', $columns))
                                    <flux:table.cell variant="strong" class="whitespace-nowrap">
                                        {{ Carbon\Carbon::parse($transaction->date)->format('M j, Y') }}
                                    </flux:table.cell>
                                @endif

                                @if (in_array('payee', $columns))
                                    <flux:table.cell variant="strong" class="whitespace-nowrap">
                                        {{ $transaction->payee }}
                                    </flux:table.cell>
                                @endif

                                @if (!$account && in_array('account', $columns))
                                    <flux:table.cell variant="strong" class="whitespace-nowrap">
                                        <flux:button
                                            href="https://google.com"
                                            wire:navigate
                                            variant="ghost"
                                            class="text-emerald-500! hover:text-emerald-600! dark:hover:text-emerald-400! hover:bg-transparent! p-0! h-0!"
                                        >
                                            {{ $transaction->account->name }}
                                        </flux:button>
                                    </flux:table.cell>
                                @endif

                                @if (in_array('category', $columns))
                                    <flux:table.cell variant="strong" class="whitespace-nowrap">
                                        {{ $transaction->category->name }}
                                    </flux:table.cell>
                                @endif

                                @if (in_array('type', $columns))
                                    <flux:table.cell variant="strong" class="whitespace-nowrap">
                                        {{ $transaction->type->label() }}
                                    </flux:table.cell>
                                @endif

                                @if (in_array('amount', $columns))
                                    <flux:table.cell variant="strong" class="whitespace-nowrap">
                                        @if (in_array($transaction->type, [
                                                TransactionType::DEBIT, 
                                                TransactionType::TRANSFER, 
                                                TransactionType::WITHDRAWAL
                                            ])
                                        )
                                            <span class="-mr-0.5">-</span>
                                        @else
                                            <span class="-mr-0.5 text-emerald-500">+</span>
                                        @endif

                                        <span @class([
                                            'text-emerald-500' => in_array($transaction->type, [
                                                TransactionType::CREDIT,
                                                TransactionType::DEPOSIT,
                                            ]),
                                        ])>
                                            ${{ Number::format($transaction->amount ?? 0, 2) }}
                                        </span>
                                    </flux:table.cell>
                                @endif

                                @if (in_array('status', $columns))
                                    <flux:table.cell class="whitespace-nowrap">
                                        <form wire:submit="toggleStatus({{ $transaction->id }})">
                                            <div wire:loading.remove
                                                wire:target="toggleStatus({{ $transaction->id }})">
                                                <flux:badge as="button" type="submit" color="{{ $transaction->status ? 'emerald' : 'amber' }}" size="sm">
                                                    {{ $transaction->status ? 'Cleared' : 'Pending' }}
                                                </flux:badge>
                                            </div>

                                            <div wire:loading wire:target="toggleStatus({{ $transaction->id }})" class="w-full place-items-center pt-1">
                                                <flux:icon.loading class="h-[16px]! mr-2" />
                                            </div>
                                        </form>
                                    </flux:table.cell>
                                @endif

                                <flux:table.cell align="end">
                                    <div class="flex items-center justify-end">
                                        @if ($transaction->attachments) 
                                            <flux:modal.trigger name="attachments">
                                                <div x-data="{ attachments: @js($transaction->attachments) }">
                                                    <flux:button
                                                        icon="photo"
                                                        variant="ghost"
                                                        size="sm"
                                                        class="!text-zinc-600 dark:!text-zinc-100"
                                                        x-on:click="$dispatch('load-attachments', { attachments })"
                                                    />
                                                </div>
                                            </flux:modal.trigger>
                                        @endif

                                        <flux:button
                                            href="{{ route('edit-transaction', $transaction) }}"
                                            wire:navigate
                                            variant="ghost"
                                            size="sm"
                                            icon="pencil-square"
                                            class="text-indigo-500!"
                                        />

                                        <div>
                                            <flux:modal.trigger name="delete-transaction-{{ $transaction->id }}">
                                                <flux:button icon="trash" variant="ghost" size="sm"
                                                    class="text-red-500!" />
                                            </flux:modal.trigger>

                                            <flux:modal name="delete-transaction-{{ $transaction->id }}" class="min-w-[22rem]">
                                                <form wire:submit="delete({{ $transaction->id }})" class="space-y-6 text-left">
                                                    <div class="space-y-4!">
                                                        <flux:heading size="lg" class="font-semibold -mt-1.5!">
                                                            Delete Transaction?
                                                        </flux:heading>

                                                        <flux:subheading>
                                                            Are you sure you want to delete this transaction?
                                                        </flux:subheading>
                                                    </div>

                                                    <div class="flex gap-2">
                                                        <flux:spacer />

                                                        <flux:modal.close>
                                                            <flux:button variant="ghost" size="sm">
                                                                Cancel
                                                            </flux:button>
                                                        </flux:modal.close>

                                                        <flux:button type="submit" variant="danger" size="sm">
                                                            Confirm
                                                        </flux:button>
                                                    </div>
                                                </form>
                                            </flux:modal>
                                        </div>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>                        
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @else
                <flux:heading class="italic! font-medium text-center pb-3">
                    No transactions found...
                </flux:heading>
            @endif
        </x-slot:content>
    </x-card>

    <livewire:attachments />
</div>