@use('App\Enums\TransactionType', 'TransactionType')

<div class="space-y-4">
    <div class="flex items-center justify-between gap-2">
        <div>
            <flux:heading size="xl">
                Report Overview
            </flux:heading>

            <p class="text-[17px]">
                {{ $report->name }}
            </p>
        </div>

        <div>
            <flux:modal.trigger name="edit-report">
                <flux:button icon="pencil-square" variant="primary" size="sm">
                    Edit
                </flux:button>
            </flux:modal.trigger>
            
            <flux:modal name="edit-report">
                <div class="space-y-6">
                    <div class="space-y-6">
                        <flux:heading size="lg" class="font-semibold -mt-1.5!">
                            Edit Report
                        </flux:heading>
        
                        <flux:field>
                            <flux:label>Name</flux:label>
        
                            <flux:input type="text" wire:model='name' />
        
                            <flux:error name="name" />
                        </flux:field>
        
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
                    </div>
                </div>
            </flux:modal>
        </div>
    </div>

    <x-card>
        <x-slot:content>
            <div class="flex items-start text-sm justify-between">
                <div class="flex flex-col space-y-2 text-[15px] w-full overflow-x-auto">
                    <div class="p-3 pb-0 flex flex-col space-y-2 text-[15px]">
                        @if ($report->account) 
                            <p>
                                <span class="font-medium">
                                    Account:
                                </span>

                                {{ $report->account->name }}
                            </p>
                        @endif

                        @if ($report->type)
                            <p>
                                <span class="font-medium">
                                    Transaction Type:
                                </span>

                                {{ $report->type->label() }}
                            </p>
                        @endif

                        @if ($report->category) 
                            <p>
                                <span class="font-medium">
                                    Category:
                                </span>
        
                                {{ $report->category->name }}
                            </p>
                        @endif

                        @if ($report->tag) 
                            <p>
                                <span class="font-medium">
                                    Tag:
                                </span>
        
                                {{ $report->tag->name }}
                            </p>
                        @endif

                        @if ($report->payees) 
                            <p>
                                <span class="font-medium">
                                    Payees:
                                </span>
        
                                {{ implode(', ', $report->payees) }}
                            </p>
                        @endif

                        <p>
                            <span class="font-medium">
                                Date:
                            </span>

                            {{ $report->start_date->format('M j, Y') }} 
                            - 
                            {{ $report->end_date->format('M j, Y') }}
                        </p>
                    </div>

                    <div class="m-3 mt-0">
                        <p class="font-medium">
                            Transactions:
                        </p>

                        <div class="rounded-[8px] shadow-xs border border-zinc-200 dark:border-white/20 mt-2">
                            @if ($report_transactions->count())
                                <div class="p-3 border-b border-zinc-200 dark:border-white/20">
                                    <flux:input icon="magnifying-glass" placeholder="Search transactions..." wire:model.live.debounce.300ms='search' clearable />
                                </div>

                                <flux:table :paginate="$report_transactions">
                                    <flux:table.columns class="[&>tr>th]:px-3! [&>tr>th]:py-2! bg-zinc-100 dark:bg-zinc-700 hidden sm:table-header-group">
                                        <flux:table.column>
                                            Date
                                        </flux:table.column>

                                        <flux:table.column>
                                            Payee
                                        </flux:table.column>

                                        <flux:table.column>
                                            Account
                                        </flux:table.column>

                                        <flux:table.column>
                                            Category
                                        </flux:table.column>

                                        <flux:table.column>
                                            Type
                                        </flux:table.column>

                                        <flux:table.column>
                                            Amount
                                        </flux:table.column>
                                    </flux:table.columns>

                                    <flux:table.rows class="sm:hidden border-b-0!">
                                        @foreach ($report_transactions as $transaction)
                                            <flux:table.row :key="$transaction->id">
                                                <flux:table.cell class="!p-0">
                                                    <x-report-transaction :$transaction />
                                                </flux:table.cell>
                                            </flux:table.row>
                                        @endforeach
                                    </flux:table.rows>

                                    <flux:table.rows class="dark:bg-zinc-900 hidden sm:table-row-group">
                                        @foreach ($report_transactions as $transaction)
                                            <flux:table.row :key="$transaction->id" class="[&>td]:p-3!">
                                                <flux:table.cell variant="strong" class="whitespace-nowrap">
                                                    {{ $transaction->date->format('M j, Y') }}
                                                </flux:table.cell>

                                                <flux:table.cell variant="strong" class="whitespace-nowrap">
                                                    {{ $transaction->payee }}
                                                </flux:table.cell>

                                                <flux:table.cell variant="strong" class="whitespace-nowrap">
                                                    {{ $transaction->snapshot['account']['name'] }}
                                                </flux:table.cell>

                                                <flux:table.cell variant="strong" class="whitespace-nowrap">
                                                    {{ $transaction->snapshot['category']['name'] }}
                                                </flux:table.cell>

                                                <flux:table.cell variant="strong" class="whitespace-nowrap">
                                                    {{ $transaction->type->label() }}
                                                </flux:table.cell>

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
                                                        ])
                                                    ])>
                                                        ${{ Number::format($transaction->amount ?? 0, 2) }}
                                                    </span>
                                                </flux:table.cell>
                                            </flux:table.row>                        
                                        @endforeach
                                    </flux:table.rows>
                                </flux:table>
                            @else
                                <flux:heading class="italic! font-medium text-center p-2">
                                    No transactions found...
                                </flux:heading>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </x-slot:content>
    </x-card>
</div>
