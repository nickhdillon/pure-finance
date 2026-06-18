<div>
    @if (request()->routeIs('dashboard'))
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
                                <span @class(['text-red-500 font-medium' => $expense->total_spent > $expense->planned_amount])>
                                    ${{ Number::format($expense->total_spent ?? 0, 2) }}
                                </span>

                                of

                                ${{ Number::format($expense->planned_amount ?? 0, 2) }}
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
    @else
        <div class="space-y-4" x-data="{ view: 'grid' }">
            <div class="flex justify-between gap-4">
                <div class="flex flex-wrap items-center justify-between w-full gap-2 sm:gap-4">
                    <flux:heading size="xl">
                        Planned Spending
                    </flux:heading>

                    <flux:radio.group variant="segmented" x-model="view" size="sm">
                        <flux:radio value="grid" icon="squares-2x2" />
                        <flux:radio value="list" icon="list-bullet" />
                    </flux:radio.group>
                </div>

                <div>
                    <flux:modal.trigger name="add-expense">
                        <flux:button icon="plus" variant="primary" size="sm">
                            Add
                        </flux:button>
                    </flux:modal.trigger>
                
                    @livewire('planned-spending-form')
                </div>
            </div>

            <div x-cloak x-show="view === 'grid'">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @forelse ($expenses as $expense)
                        <a href="{{ route('planned-expense-view', $expense) }}" wire:navigate
                            class="block rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 duration-200 ease-in-out hover:bg-zinc-50/80 dark:hover:bg-zinc-600/50 shadow-xs"
                        >
                            <div class="flex items-center gap-2 mb-3">
                                <p class="font-medium">
                                    {{ $expense->name }}
                                </p>

                                <flux:badge
                                    size="sm"
                                    :color="$expense->type->color()"
                                >
                                    {{ $expense->type->label() }}
                                </flux:badge>
                            </div>

                            <div class="w-full my-1.5 h-8 sm:h-9 bg-zinc-50 dark:bg-zinc-700 shadow-sm rounded-lg">
                                <div
                                    @class([
                                        '!rounded-r-lg' => $expense->percentage_spent >= 100,
                                        '!bg-red-500 hover:!bg-red-400' => $expense->percentage_spent > 100,
                                        'min-w-[25px]' => $expense->percentage_spent > 0,
                                        '!bg-transparent' => $expense->percentage_spent === 0,
                                        'flex items-center justify-center h-full bg-emerald-500 hover:bg-emerald-400 rounded-lg cursor-pointer rounded-r-none duration-200 ease-in-out text-sm',
                                    ])
                                    style="width: {{ min($expense->percentage_spent, 100) }}%;"
                                >
                                    <span x-cloak x-show="@js($expense->percentage_spent) > 0"
                                        class="font-semibold text-white"
                                    >
                                        {{ Number::format($expense->percentage_spent, 0) }}%
                                    </span>
                                </div>
                            </div>

                            <p class="text-sm">
                                <span @class(['text-red-500 font-medium' => $expense->total_spent > $expense->planned_amount])>
                                    ${{ Number::format($expense->total_spent ?? 0, 2) }}
                                </span>

                                of

                                ${{ Number::format($expense->planned_amount ?? 0, 2) }}
                            </p>
                        </a>
                    @empty
                        <div
                            class="p-2.5 text-sm italic font-medium text-center text-zinc-800 whitespace-nowrap dark:text-zinc-200 col-span-full">
                            No expenses found...
                        </div>
                    @endforelse
                </div>

                <div class="flex items-center space-x-2 py-2.5 text-sm col-span-full">
                    <p class="font-medium">Total:</p>

                    <p>
                        <span @class(['text-red-500 font-medium' => $total_spent > $total_planned])>
                            ${{ Number::format($total_spent ?? 0, 2) }}
                        </span>

                        of

                        ${{ Number::format($total_planned ?? 0, 2) }}
                    </p>
                </div>
            </div>

            <x-card x-cloak x-show="view === 'list'">
                <x-slot:content>
                    <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse ($expenses as $expense)
                            <a href="{{ route('planned-expense-view', $expense) }}" wire:navigate
                                class="flex items-center justify-between p-3 text-sm duration-200 ease-in-out first:rounded-t-[8px] last:rounded-b-[8px] hover:bg-zinc-50/80 dark:hover:bg-zinc-600/50"
                            >
                                <div class="flex items-center gap-2">
                                    <p class="font-medium">
                                        {{ $expense->name }}
                                    </p>

                                    <flux:badge
                                        size="sm"
                                        :color="$expense->type->color()"
                                    >
                                        {{ $expense->type->label() }}
                                    </flux:badge>
                                </div>
            
                                <p>
                                    <span @class(['text-red-500 font-medium' => $expense->total_spent > $expense->planned_amount])>
                                        ${{ Number::format($expense->total_spent ?? 0, 2) }}
                                    </span>

                                    of

                                    ${{ Number::format($expense->planned_amount ?? 0, 2) }}
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
    @endif
</div>
