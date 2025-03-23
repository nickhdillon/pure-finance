<div x-on:transaction-deleted.window="$wire.$refresh">
    <x-card heading="Accounts">
        <x-slot:button>
            @livewire('account-form')
        </x-slot:button>
    
        <x-slot:content>
            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($accounts as $account)
                    <a href="/" wire:navigate
                        class="flex flex-col p-3 text-sm duration-200 ease-in-out first:rounded-t-[8px] last:rounded-b-[8px] hover:bg-zinc-50/80 dark:hover:bg-zinc-600/50">
                        <p class="font-medium">
                            {{ $account->name }}
                        </p>
    
                        <div class="w-full">
                            @if ($account->transactions_count === 0)
                                ${{ Number::format($account->initial_balance ?? 0, 2) }}
                            @else
                                <div class="flex items-center justify-between">
                                    <span>
                                        Available:
        
                                        ${{ Number::format($account->balance ?? 0, 2) }}
                                    </span>
        
                                    <span>
                                        Cleared:
        
                                        ${{ Number::format($account->cleared_balance ?? 0, 2) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </a>
                @empty
                    <div
                        class="p-2.5 text-sm italic font-medium text-center text-zinc-800 whitespace-nowrap dark:text-zinc-200">
                        No accounts found...
                    </div>
                @endforelse

                @if ($accounts->count() > 0)
                    <div class="flex items-center justify-between p-3 text-sm rounded-b-[8px] w-full">
                        <div class="flex items-center gap-1">
                            <p class="font-medium">
                                Available Total:
                            </p>

                            <p>
                                ${{ Number::format($available_total ?? 0, 2) }}
                            </p>
                        </div>

                        <div class="flex items-center gap-1">
                            <p class="font-medium">
                                Cleared Total:
                            </p>

                            <p>
                                ${{ Number::format($cleared_total ?? 0, 2) }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </x-slot:content>
    </x-card>
</div>
