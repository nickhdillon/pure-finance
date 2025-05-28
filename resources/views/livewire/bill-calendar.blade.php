<div x-data="calendar" class="space-y-4 w-full">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">
            Bill Calendar
        </flux:heading>

        <flux:button
            href="#"
            wire:navigate variant="primary" icon="plus" size="sm">
            Add
        </flux:button>
    </div>

    <x-card>
        <x-slot:content>                
            <div class="p-3 gap-2.5 flex items-center justify-between dark:bg-zinc-900 rounded-t-[8px]">
                <flux:heading class="text-xl" x-text="monthLabel"></flux:heading>

                <flux:button.group>
                    <flux:button x-on:click="changeMonth(-1)" class="px-2!" variant="outline" size="sm">
                        <flux:icon.chevron-left icon-variant="outline" class="h-[14px] w-[14px] stroke-2" />
                    </flux:button>

                    <flux:button size="sm" x-on:click="goToToday" class="px-4!">
                        Today
                    </flux:button>

                    <flux:button x-on:click="changeMonth(1)" class="px-2!" variant="outline" size="sm">
                        <flux:icon.chevron-right icon-variant="outline" class="h-[14px] w-[14px] stroke-2" />
                    </flux:button>
                </flux:button.group>
            </div>

            <div class="grid grid-cols-7 border-y border-zinc-200 dark:border-white/20 text-center font-medium bg-zinc-100 text-sm dark:bg-zinc-800 py-2">
                <template x-for="(day, index) in dayNames" :key="index">
                    <div>
                        <div class="text-zinc-800 dark:text-zinc-100 text-sm font-medium text-center lg:hidden" x-text="day.substring(0,3)"></div>
                        <div class="text-zinc-800 dark:text-zinc-100 text-sm font-medium text-center hidden lg:block" x-text="day"></div>
                    </div>
                </template>
            </div>

            <div class="rounded-b-[8px] overflow-hidden">
                <div class="grid grid-cols-7 gap-px bg-zinc-200 dark:bg-zinc-600">
                    <template x-for="(day, index) in days" :key="index">
                        <div class="p-1 h-[140px] overflow-scroll text-sm text-left flex flex-col"
                            :class="{
                                'bg-white dark:bg-zinc-900': !day.blank,
                                'text-zinc-400 dark:text-zinc-500 bg-striped dark:bg-striped': day.blank,
                            }"
                        >
                            <div class="flex flex-col h-full">
                                <div class="sticky top-0 z-10 font-medium py-0.5 px-1 shrink-0 w-fit rounded-full" 
                                    :class="day.isToday ? 'bg-emerald-500 text-white' : ''" 
                                    x-text="day.day">
                                </div>
                
                                <div class="overflow-y-auto p-1 gap-1 flex flex-col">
                                    <template x-for="bill in day.bills" :key="bill.id">
                                        <flux:modal.trigger name="bill-details">
                                            <flux:button type="button" class="text-xs bg-emerald-100! dark:bg-emerald-900! text-emerald-800! h-auto! dark:text-emerald-100! px-1! py-0.5! rounded! justify-start! border-none! cursor-pointer">
                                                <p x-text="bill.name" class="truncate"></p>
                                            </flux:button>
                                        </flux:modal.trigger>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>          
        </x-slot:content>
    </x-card>

    <x-bill-details />
</div>

@script
    <script>
        Alpine.data('calendar', () => {
            return {
                today: new Date(),
                current: null,
                dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                days: [],
                monthLabel: '',
                bills: [
                    { id: 1, name: 'Electricity', dueDate: '2025-05-03' },
                    { id: 4, name: 'Test', dueDate: '2025-05-03' },
                    { id: 6, name: 'Test 2', dueDate: '2025-05-03' },
                    { id: 7, name: 'Test 3', dueDate: '2025-05-03' },
                    { id: 8, name: 'Test 4', dueDate: '2025-05-03' },
                    { id: 9, name: 'Test 5', dueDate: '2025-05-03' },
                    { id: 10, name: 'Test 6', dueDate: '2025-05-03' },
                    { id: 11, name: 'Test 7', dueDate: '2025-05-03' },
                    { id: 12, name: 'Test 8', dueDate: '2025-05-03' },
                    { id: 2, name: 'Water', dueDate: '2025-05-12' },
                    { id: 3, name: 'Internet', dueDate: '2025-05-22' },
                ],

                init() {
                    this.current = this.getMonthStart(new Date());
                    this.refresh();
                },

                goToToday() {
                    this.current = this.getMonthStart(this.today);
                    this.refresh();
                },

                formatDate(date) {
                    return date.toISOString().split('T')[0];
                },

                changeMonth(offset) {
                    this.current = this.getMonthStart(new Date(this.current.getFullYear(), this.current.getMonth() + offset, 1));
                    this.refresh();
                },

                refresh() {
                    this.monthLabel = this.current.toLocaleDateString('default', {
                        month: 'long',
                        year: 'numeric',
                    });

                    this.days = this.generateDays();
                },

                getMonthStart(date) {
                    return new Date(date.getFullYear(), date.getMonth(), 1);
                },

                generateDays() {
                    const year = this.current.getFullYear();
                    const month = this.current.getMonth();
                    const startDay = new Date(year, month, 1).getDay();
                    const lastDate = new Date(year, month + 1, 0).getDate();
                    const days = [];

                    // Previous month days
                    const prevMonthLastDate = new Date(year, month, 0).getDate();

                    for (let i = startDay - 1; i >= 0; i--) {
                        const date = new Date(year, month - 1, prevMonthLastDate - i);

                        days.push({
                            blank: true,
                            day: date.getDate(),
                            date: this.formatDate(date),
                            bills: [],
                            isToday: this.formatDate(date) === this.formatDate(this.today)
                        });
                    }

                    // Current month days
                    for (let i = 1; i <= lastDate; i++) {
                        const date = new Date(year, month, i);
                        const dateStr = this.formatDate(date);

                        days.push({
                            blank: false,
                            day: i,
                            date: dateStr,
                            bills: this.bills.filter(b => b.dueDate === dateStr),
                            isToday: dateStr === this.formatDate(this.today),
                        });
                    }

                    // Fill next month
                    const remainder = days.length % 7;
                    const nextDaysNeeded = remainder === 0 ? 0 : 7 - remainder;

                    for (let i = 1; i <= nextDaysNeeded; i++) {
                        const date = new Date(year, month + 1, i);

                        days.push({
                            blank: true,
                            day: i,
                            date: this.formatDate(date),
                            bills: [],
                            isToday: this.formatDate(date) === this.formatDate(this.today)
                        });
                    }

                    return days;
                }
            };
        });
    </script>
@endscript
