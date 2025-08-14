<div class="space-y-4">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">
            Reports
        </flux:heading>

        <flux:button
            href="{{ route('report-form') }}"
            wire:navigate variant="primary" icon="plus" size="sm">
            Add
        </flux:button>
    </div>

    <x-card>
        <x-slot:content>                
            <div class="p-3 dark:bg-zinc-900 rounded-t-[8px]">
                <flux:input icon="magnifying-glass" placeholder="Search reports..." wire:model.live.debounce.300ms='search' clearable />
            </div>

            @if ($reports->count() > 0)
                <flux:table :paginate="$reports" class="border-t border-zinc-200 dark:border-white/20">
                    <flux:table.columns class="[&>tr>th]:!px-3 hidden sm:table-header-group bg-zinc-50 dark:bg-zinc-800">
                        <flux:table.column>
                            Name
                        </flux:table.column>

                        <flux:table.column>
                            Category
                        </flux:table.column>

                        <flux:table.column>
                            Date
                        </flux:table.column>

                        <flux:table.column align="end">
                            Actions
                        </flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows class="sm:hidden dark:bg-zinc-900 border-b-0!">
                        @foreach ($reports as $report)
                            <flux:table.row :key="$report->id" class="hover:bg-zinc-100 sm:hidden">
                                <flux:table.cell class="p-0!">
                                    <a href="{{ route('report-view', $report) }}" wire:navigate class="font-medium text-zinc-800 dark:text-white w-full flex text-center p-3">
                                        {{ $report->name }}
                                    </a>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>

                    <flux:table.rows class="hidden sm:table-row-group dark:bg-zinc-900">
                        @foreach ($reports as $report)
                            <flux:table.row :key="$report->id" class="[&>td]:px-3! [&>td]:py-2.5!">
                                <flux:table.cell variant="strong" class="whitespace-nowrap">
                                    {{ $report->name }}
                                </flux:table.cell>

                                <flux:table.cell variant="strong" class="whitespace-nowrap">
                                    {{ $report->category->name ?? 'N/A' }}
                                </flux:table.cell>

                                <flux:table.cell variant="strong" class="whitespace-nowrap">
                                    {{ $report->start_date->format('M j, Y') }} 
                                    - 
                                    {{ $report->end_date->format('M j, Y') }}
                                </flux:table.cell>

                                <flux:table.cell align="end">
                                    <div class="flex items-center justify-end">
                                        <flux:button :href="route('report-view', $report)"
                                            wire:navigate
                                            variant="ghost"
                                            size="sm"
                                            icon="eye"
                                            class="text-zinc-700! dark:text-white!"
                                        />

                                        <div>
                                            <flux:modal.trigger name="delete-report-{{ $report->id }}">
                                                <flux:button icon="trash" variant="ghost" size="sm"
                                                    class="text-red-500!" />
                                            </flux:modal.trigger>

                                            <flux:modal name="delete-report-{{ $report->id }}" class="min-w-[22rem]">
                                                <form wire:submit="delete({{ $report->id }})" class="space-y-6 text-left">
                                                    <div class="space-y-4!">
                                                        <flux:heading size="lg" class="font-semibold -mt-1.5!">
                                                            Delete Report?
                                                        </flux:heading>

                                                        <flux:subheading>
                                                            Are you sure you want to delete this report?
                                                        </flux:subheading>
                                                    </div>

                                                    <div class="flex gap-2">
                                                        <flux:spacer />

                                                        <flux:modal.close>
                                                            <flux:button variant="ghost" size="sm">
                                                                Cancel
                                                            </flux:button>
                                                        </flux:modal.close>

                                                        <flux:button type="submit" variant="danger" size="sm">
                                                            Confirm
                                                        </flux:button>
                                                    </div>
                                                </form>
                                            </flux:modal>
                                        </div>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @else
                <flux:heading class="italic! font-medium text-center pb-3">
                    No reports found...
                </flux:heading>
            @endif
        </x-slot:content>
    </x-card>
</div>
