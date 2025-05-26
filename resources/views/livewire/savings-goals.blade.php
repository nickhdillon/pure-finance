<div>
    <x-card heading="Savings Goals">
        <x-slot:button>
            <flux:button
                href="{{ route('create-savings-goal') }}"
                wire:navigate variant="primary" icon="plus" size="sm">
                Add
            </flux:button>
        </x-slot:button>
    
        <x-slot:content>
            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($savings_goals as $savings_goal)
                    <a href="{{ route('savings-goal-view', $savings_goal) }}" wire:navigate
                        class="flex items-center justify-between p-3 text-sm duration-200 ease-in-out first:rounded-t-[8px] last:rounded-b-[8px] hover:bg-zinc-50/80 dark:hover:bg-zinc-600/50">
                        <p class="font-medium">
                            {{ $savings_goal->name }}
                        </p>
    
                        <p>
                            <span @class([
                            'text-emerald-500 font-medium' =>
                                $savings_goal->amount_saved > $savings_goal->goal_amount,
                            ])>
                                ${{ Number::format($savings_goal->amount_saved ?? 0, 2) }}
                            </span>

                            of

                            ${{ Number::format($savings_goal->goal_amount ?? 0, 2) }}
                        </p>
                    </a>
                @empty
                    <div
                        class="p-2.5 text-sm italic font-medium text-center text-zinc-800 whitespace-nowrap dark:text-zinc-200">
                        No savings goals found...
                    </div>
                @endforelse
            </div>
        </x-slot:content>
    </x-card>
</div>
