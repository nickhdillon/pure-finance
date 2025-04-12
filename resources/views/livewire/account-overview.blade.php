<div class="space-y-4" x-on:account-saved="$wire.$refresh" x-on:transaction-deleted.window="$wire.$refresh" x-on:status-changed.window="$wire.$refresh">
    <flux:heading size="xl">
        Account Overview
    </flux:heading>

    <div class="flex flex-col gap-6">
        <x-card heading="Details">
            <x-slot:button>
                <div>
                    <flux:modal.trigger name="{{ $account ? ('edit-account' . $account->id) : 'add-account' }}">
                        <flux:button icon="pencil-square" variant="primary" size="sm">
                            Edit
                        </flux:button>
                    </flux:modal.trigger>

                    <livewire:account-form :$account />
                </div>
            </x-slot:button>

            <x-slot:content>
                <div class="p-3 flex items-start text-sm justify-between">
                    <div class="flex flex-col space-y-2">
                        <p>
                            <span class="font-medium">
                                Name:
                            </span>

                            {{ $account->name }}
                        </p>

                        <p>
                            <span class="font-medium">
                                Type:
                            </span>

                            {{ $account->type->label() }}
                        </p>
                    </div>

                    <div class="flex flex-col">
                        @if ($account->transactions()->count() === 0)
                            <p>
                                <span class="font-medium">
                                    Initial Balance:
                                </span>

                                ${{ Number::format($account->initial_balance ?? 0, 2) }}
                            </p>
                        @else
                            <div class="space-y-2">
                                <p>
                                    <span class="font-medium">
                                        Available:
                                    </span>

                                    ${{ Number::format($account->balance ?? 0, 2) }}
                                </p>

                                <p>
                                    <span class="font-medium">
                                        Cleared:
                                    </span>

                                    ${{ Number::format($account->cleared_balance ?? 0, 2) }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </x-slot:content>
        </x-card>

        <livewire:transaction-table :$account />
    </div>
</div>
