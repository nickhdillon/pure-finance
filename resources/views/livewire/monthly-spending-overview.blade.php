<div>
    <x-card>
        <x-slot:content>                
            <div class="p-3 gap-2 flex flex-col dark:bg-zinc-900 w-full">
                <flux:heading class="text-xl text-center sm:text-left pt-2 sm:pt-0">
                    Total Spending for

                    <span class="sm:hidden">{{ $month_short }}</span>
                    <span class="hidden sm:inline">{{ $month_full }}</span>
                </flux:heading>
            
                <div class="flex flex-col sm:flex-row p-3 gap-5 w-full items-center justify-between">
                    <div class="relative w-46 h-46 rounded-full">
                        <div class="absolute inset-0 rounded-full" style="background: conic-gradient({{ $gradient }});"></div>
            
                        <div class="absolute inset-[20px] flex justify-center items-center bg-white dark:bg-zinc-900 rounded-full text-lg font-medium">
                            ${{ Number::format($monthly_total, 2) }}
                        </div>
                    </div>
            
                    <ul class="space-y-3 sm:ml-auto">
                        @foreach ($top_categories as $category)
                            <li class="flex text-sm items-center gap-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="inline-block w-3 h-3 rounded-full {{ $category->color }}"></span>
                                    
                                    <span class="font-medium">
                                        {{ $category->name }}
                                    </span>
                                </div>

                                <p>-</p>

                                <div class="flex gap-1">
                                    <span class="font-medium">
                                        {{ Number::format($category->percent, 1) }}%
                                    </span>

                                    (${{ Number::format($category->total_spent, 2) }})
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>            
        </x-slot:content>
    </x-card>
</div>
