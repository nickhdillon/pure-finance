<x-layouts.app title="Dashboard">
    <div class="flex flex-col gap-6">
        @livewire('accounts')

        @livewire('planned-spending')

        @livewire('transaction-table')
    </div>
</x-layouts.app>
