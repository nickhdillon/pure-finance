<div x-on:transaction-deleted.window="$wire.$refresh">
    <x-card heading="Accounts">
        <x-slot:button>
            <div>
                <flux:modal.trigger name="add-account">
                    <flux:button icon="plus" variant="primary" size="sm">
                        Add
                    </flux:button>
                </flux:modal.trigger>

                @livewire('account-form')
            </div>
        </x-slot:button>
    
        <x-slot:content>
            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($grouped_accounts as $group_name => $group)
                    <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        <p @class([
                            'text-emerald-600 dark:text-emerald-400' => $group_name === 'banking',
                            'text-red-600 dark:text-red-400' => $group_name === 'credit',
                            'text-blue-600 dark:text-blue-400' => $group_name === 'investment',
                            'text-sm px-3 py-2.5 font-semibold uppercase'
                        ])>
                            {{ $group_name }}
                        </p>

                        <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach ($group['accounts'] as $account)
                                <a href="{{ route('account-overview', $account) }}" wire:navigate
                                class="flex flex-col p-3 text-sm duration-200 ease-in-out hover:bg-zinc-50/80 dark:hover:bg-zinc-700/50">
                                    <p class="font-medium">{{ $account->name }}</p>

                                    <div class="w-full">
                                        @if ($account->transactions_count === 0)
                                            ${{ Number::format($account->initial_balance ?? 0, 2) }}
                                        @else
                                            <div class="flex items-center justify-between">
                                                <span>
                                                    Available: ${{ Number::format($account->balance ?? 0, 2) }}
                                                </span>

                                                <span>
                                                    Cleared: ${{ Number::format($account->cleared_balance ?? 0, 2) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <div class="flex items-center justify-between py-2.5 px-3 gap-2 text-sm w-full bg-zinc-50/50 dark:bg-zinc-800">
                            <div class="flex flex-col sm:flex-row sm:space-x-1">
                                <p class="font-medium">Available Total:</p>

                                <p>${{ Number::format($group['available_total'] ?? 0, 2) }}</p>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:space-x-1">
                                <p class="font-medium">Cleared Total:</p>
                                
                                <p class="text-right">${{ Number::format($group['cleared_total'] ?? 0, 2) }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="p-2.5 text-sm italic font-medium text-center text-zinc-800 whitespace-nowrap dark:text-zinc-200">
                        No accounts found...
                    </div>
                @endforelse
            </div>
        </x-slot:content>
    </x-card>
</div>
