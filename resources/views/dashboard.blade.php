<x-layouts.app>
    <div class="flex flex-col gap-6">
        @if (auth()->user()->categories()->count()) 
            <x-overview-slider />
        @endif

        <livewire:accounts />

        <livewire:planned-spending />

        <livewire:savings-goals />

        <livewire:upcoming-bills />

        <livewire:transaction-table lazy />
    </div>
</x-layouts.app>
