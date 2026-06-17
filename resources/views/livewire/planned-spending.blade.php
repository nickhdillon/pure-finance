<div>
    <x-card heading="Planned Spending">
        <x-slot:button>
            <div>
                <flux:modal.trigger name="add-expense">
                    <flux:button icon="plus" variant="primary" size="sm">
                        Add
                    </flux:button>
                </flux:modal.trigger>

                @livewire('planned-spending-form')
            </div>
        </x-slot:button>
    
        <x-slot:content>
            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($expenses as $expense)
                    <a href="{{ route('planned-expense-view', $expense) }}" wire:navigate
                        class="flex items-center justify-between p-3 text-sm duration-200 ease-in-out first:rounded-t-[8px] last:rounded-b-[8px] hover:bg-zinc-50/80 dark:hover:bg-zinc-600/50">
                        <p class="font-medium">
                            {{ $expense->name }}
                        </p>
    
                        <p>
                            <span @class(['text-red-500 font-medium' => $expense->total_spent > $expense->monthly_amount])>
                                ${{ Number::format($expense->total_spent ?? 0, 2) }}
                            </span>

                            of

                            ${{ Number::format($expense->monthly_amount ?? 0, 2) }}
                        </p>
                    </a>
                @empty
                    <div
                        class="p-2.5 text-sm italic font-medium text-center text-zinc-800 whitespace-nowrap dark:text-zinc-200">
                        No expenses found...
                    </div>
                @endforelse

                <div class="flex items-center justify-between bg-zinc-100/50 dark:bg-zinc-800 space-x-1 py-2.5 px-3 text-sm w-full">
                    <p class="font-medium">Total</p>

                    <p>
                        <span @class(['text-red-500 font-medium' => $total_spent > $total_planned])>
                            ${{ Number::format($total_spent ?? 0, 2) }}
                        </span>

                        of

                        ${{ Number::format($total_planned ?? 0, 2) }}
                    </p>
                </div>
            </div>
        </x-slot:content>
    </x-card>
</div>
