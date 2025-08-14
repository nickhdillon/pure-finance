<div class="space-y-4 max-w-4xl mx-auto">
    <flux:heading size="xl">
        New Report
    </flux:heading>

    <form wire:submit='submit' class="space-y-5">
        <x-card>
            <x-slot:content>
                <div class="p-5 grid items-start grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="space-y-5">
                        <flux:field>
                            <flux:label>Account</flux:label>

                            <flux:select variant="listbox" placeholder="Select an account"
                                wire:model='account_id' clearable searchable>
                                @foreach ($accounts as $account)
                                    <flux:select.option value="{{ $account->id }}">
                                        {{ $account->name }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>

                            <flux:error name="account_id" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Transaction Type</flux:label>

                            <flux:select variant="listbox" placeholder="Select a type" wire:model='type' clearable searchable>
                                @foreach ($transaction_types as $transaction_type)
                                    <flux:select.option value="{{ $transaction_type->value }}">
                                        {{ $transaction_type->label() }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>

                            <flux:error name="type" />
                        </flux:field>

                        <x-payees :$user_payees />
                    </div>

                    <div class="space-y-5">
                        <x-categories :$categories hide_form />

                        <flux:field>
                            <flux:label>Tag</flux:label>

                            <flux:select variant="listbox" placeholder="Select a tag" wire:model='tag_id' clearable searchable>
                                @foreach ($tags as $tag)
                                    <flux:select.option value="{{ $tag['id'] }}">
                                        {{ $tag['name'] }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>

                            <flux:error name="tag" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Date Range</flux:label>

                            <flux:date-picker mode="range" wire:model='date_range' clearable
                                presets="thisWeek last7Days thisMonth last3Months last6Months yearToDate custom" />

                            <flux:error name="date_range" />
                        </flux:field>
                    </div>
                
                    <div class="sm:col-start-2 flex justify-end gap-2">
                        <flux:button href="{{ route('reports') }}"
                            wire:navigate variant="outline" class="px-4!" size="sm">
                            Cancel
                        </flux:button>
                
                        <flux:button variant="primary" class="px-4!" size="sm" type="submit">
                            Generate
                        </flux:button>
                    </div>
                </div>
            </x-slot:content>
        </x-card>
    </form>
</div>
