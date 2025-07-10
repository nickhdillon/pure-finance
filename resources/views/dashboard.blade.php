<x-layouts.app>
    <div class="flex flex-col gap-6">
        @livewire('monthly-spending-overview')

        @livewire('accounts')

        @livewire('planned-spending')

        @livewire('savings-goals')

        @livewire('upcoming-bills')

        @livewire('transaction-table')
    </div>
</x-layouts.app>
