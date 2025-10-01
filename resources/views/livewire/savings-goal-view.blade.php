<div class="space-y-4">
    <flux:heading size="xl">
        Savings Goal
    </flux:heading>

    <x-card heading="{{ $savings_goal->name }}">
        <x-slot:button>
            <flux:dropdown>
                <flux:button icon="ellipsis-horizontal-circle"
                    icon-variant="outline" 
                    variant="ghost"
                    size="sm"
                />

                <flux:menu>
                    <flux:menu.item href="{{ route('edit-savings-goal', $savings_goal) }}"
                        wire:navigate icon="pencil-square" icon-variant="outline">
                        Edit
                    </flux:menu.item>

                    <flux:menu.item icon="plus-circle" icon-variant="outline">
                        <flux:modal.trigger name="contribute-withdraw-form"
                            x-on:click="$flux.modal('contribute-withdraw-form').show();
                            $dispatch('open-form', { header: 'Contribute to goal' })"
                        >
                            Contribute to goal
                        </flux:modal.trigger>
                    </flux:menu.item>

                    <flux:menu.item icon="minus-circle" icon-variant="outline">
                        <flux:modal.trigger name="contribute-withdraw-form"
                            x-on:click="$flux.modal('contribute-withdraw-form').show();
                            $dispatch('open-form', { header: 'Withdraw from goal' })"
                        >
                            Withdraw from goal
                        </flux:modal.trigger>
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </x-slot:button>

        <x-slot:content>
            <div class="flex flex-col sm:flex-row justify-between">
                <div class="flex flex-col w-full">
                    <div class="flex flex-col px-4 py-3 space-y-2 text-sm">
                        <h3 class="font-medium uppercase">
                            Progress
                        </h3>

                        <div>
                            <div class="flex items-center justify-between">
                                <div class="flex gap-1">
                                    <p>Saved so far:</p>
                                    
                                    <div class="flex items-center justify-between">
                                        <h3 @class([
                                            '!text-emerald-500' => $percentage_saved >= 100,
                                            'font-semibold',
                                        ])>
                                            ${{ Number::format($total_saved ?? 0, 2) }}
                                        </h3>
                                    </div>
                                </div>

                                <div class="flex gap-1">
                                    <p>Goal:</p>

                                    <span class="font-semibold">
                                        ${{ Number::format($savings_goal->goal_amount ?? 0, 2) }}
                                    </span>
                                </div>
                            </div>

                            <div class="w-full my-1.5 h-8 sm:h-9 bg-zinc-50 dark:bg-zinc-700 shadow-sm rounded-lg">
                                <div @class([
                                    '!rounded-r-lg' => $percentage_saved >= 100,
                                    'min-w-[25px]' => $percentage_saved > 0,
                                    '!bg-transparent' => $percentage_saved === 0,
                                    'flex items-center justify-center h-full bg-emerald-500 rounded-lg rounded-r-none',
                                ])
                                    style="width: {{ min($percentage_saved, 100) }}%;">
                                    <span x-cloak x-show="$wire.percentage_saved > 0"
                                        class="font-semibold text-white">
                                        {{ Number::format($percentage_saved, 0) }}%
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex gap-1">
                                    <p>Spent:</p>

                                    <span class="font-semibold">
                                        ${{ Number::format($total_spent ?? 0, 2) }}
                                    </span>
                                </div>

                                <div class="flex gap-1">
                                    <p>Left to save:</p>

                                    <span class="font-semibold">
                                        ${{ Number::format($left_to_save ?? 0, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($savings_goal->transactions()->count())
                    <div class="w-full px-4 py-3">
                        <h3 class="sm:pl-8 text-sm font-medium uppercase">
                            History
                        </h3>

                        <div class="relative flex flex-col divide-y divide-zinc-200 dark:divide-zinc-600 sm:mx-8 text-sm">
                            @foreach ($savings_goal->transactions()->latest()->get() as $transaction)
                                <div class="py-2">
                                    @if ($transaction->contribution_amount) 
                                        <div class="flex items-center justify-between font-medium">
                                            <p>
                                                + ${{ Number::format($transaction->contribution_amount ?? 0, 2) }}
                                            </p>

                                            <p>
                                                {{ $transaction->created_at->format('M j, Y') }}
                                            </p>
                                        </div>
                                    @else
                                        <div class="flex items-center justify-between font-medium">
                                            <p>
                                                - ${{ Number::format($transaction->withdrawal_amount ?? 0, 2) }}
                                            </p>

                                            <p>
                                                {{ $transaction->created_at->format('M j, Y') }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </x-slot:content>
    </x-card>

    <livewire:contribute-withdraw-form :$savings_goal />
</div>
