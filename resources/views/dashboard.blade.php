<x-layouts.app>
    <div class="flex flex-col gap-6">
        @if (auth()->user()->categories()->count()) 
            <livewire:monthly-spending-overview />
        @endif

        <livewire:accounts />

        <livewire:transaction-table defer />

        <livewire:planned-spending />

        <livewire:savings-goals />

        <livewire:upcoming-bills />

        <livewire:net-worth-history />
    </div>
</x-layouts.app>
