<div>
    <x-card heading="Upcoming Bills">
        <x-slot:button>
            <flux:heading class="text-[18px]">
                {{ $today }} - {{ $end_of_week }}
            </flux:heading>
        </x-slot:button>

        <x-slot:content>
            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($upcoming_bills as $upcoming_bill)
                    <a href="{{ route('bill-calendar', $upcoming_bill) }}" wire:navigate
                        class="flex items-center justify-between w-full p-3 text-sm duration-200 ease-in-out first:rounded-t-[8px] last:rounded-b-[8px] hover:bg-zinc-50/80 dark:hover:bg-zinc-600/50">
                        <p class="font-medium">
                            {{ $upcoming_bill->name }} - ${{ Number::format($upcoming_bill->amount, 2) }}
                        </p>
    
                        <p>
                            {{ $upcoming_bill->date->format('n/d') }}
                        </p>
                    </a>
                @empty
                    <div
                        class="p-2.5 text-sm italic font-medium text-center text-zinc-800 whitespace-nowrap dark:text-zinc-200">
                        No bills found...
                    </div>
                @endforelse
            </div>
        </x-slot:content>
    </x-card>
</div>
