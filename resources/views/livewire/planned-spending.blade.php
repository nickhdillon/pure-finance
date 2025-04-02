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
                            <span @class([
                            'text-red-500 font-medium' =>
                                $expense->total_spent > $expense->monthly_amount,
                            ])>
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
            </div>
        </x-slot:content>
    </x-card>
</div>
