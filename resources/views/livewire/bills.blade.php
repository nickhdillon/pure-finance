@use('Illuminate\Support\Carbon', 'Carbon')

<div x-data="calendar" x-on:bill-submitted.window="setCurrentMonth" class="space-y-4 w-full">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">
            Bills
        </flux:heading>

        <flux:modal.trigger x-on:click="setCurrentMonth(); $flux.modal('bill-form').show()">
            <flux:button icon="plus" variant="primary" size="sm">
                Add
            </flux:button>
        </flux:modal.trigger>
    </div>

    <div class="flex items-center justify-between gap-4">
        {{-- <flux:input icon="magnifying-glass" placeholder="Search bills..." class="max-w-[250px]" /> --}}

        <div class="flex items-center gap-1.5">
            <flux:badge variant="pill" color="emerald" class="h-6!">Paid</flux:badge>
            <flux:badge variant="pill" color="amber" class="h-6!">Unpaid</flux:badge>
        </div>

        <flux:radio.group wire:model.live="view" variant="segmented" size="sm" aria-label="Bill calendar view">
            <flux:radio value="calendar" icon="calendar-days" />
            <flux:radio value="list" icon="list-bullet" />
        </flux:radio.group>
    </div>

    <section aria-label="Monthly bill overview" class="grid grid-cols-2 gap-2 sm:grid-cols-4 sm:gap-3">
        <flux:card class="p-3 space-y-0.25">
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Bills</p>

            <p
                class="text-lg font-semibold text-zinc-900 dark:text-white"
                x-text="currentMonthBillCount"
            >
            </p>

            <p
                class="text-xs text-zinc-500 dark:text-zinc-400"
                x-text="currentMonthBillCount === 1 ? 'Scheduled bill' : 'Scheduled bills'"
            >
                Scheduled bills
            </p>
        </flux:card>

        <flux:card class="p-3 space-y-0.25">
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Monthly total</p>

            <p
                class="text-lg font-semibold text-zinc-900 dark:text-white"
                x-text="formatAmount(currentMonthTotal)"
            >
            </p>

            <p class="text-xs text-zinc-500 dark:text-zinc-400">Scheduled</p>
        </flux:card>

        <flux:card class="border-emerald-200! bg-emerald-50/50! p-3 dark:border-emerald-400/20! dark:bg-emerald-400/10! space-y-0.25">
            <p class="text-xs font-medium text-emerald-700 dark:text-emerald-300">Paid</p>

            <p
                class="text-lg font-semibold text-emerald-700 dark:text-emerald-200"
                x-text="formatAmount(currentMonthPaidTotal)"
            >
            </p>

            <p
                class="text-xs text-emerald-700/75 dark:text-emerald-300/75"
                x-text="billCountLabel(currentMonthPaidCount)"
            >
            </p>
        </flux:card>

        <flux:card class="border-amber-200! bg-amber-50/50! p-3 dark:border-amber-400/20! dark:bg-amber-400/10! space-y-0.25">
            <p class="text-xs font-medium text-amber-700 dark:text-amber-300">Unpaid</p>

            <p
                class="text-lg font-semibold text-amber-700 dark:text-amber-200"
                x-text="formatAmount(currentMonthUnpaidTotal)"
            >
            </p>

            <p
                class="text-xs text-amber-700/75 dark:text-amber-300/75"
                x-text="billCountLabel(currentMonthUnpaidCount)"
            >
            </p>
        </flux:card>
    </section>

    @if ($view === 'calendar')
        <x-card dynamic-height>
            <x-slot:content>
                <div class="shrink-0">
                    <div class="p-3 gap-2.5 flex items-center justify-between dark:bg-zinc-900 rounded-t-[8px]">
                        <flux:heading class="text-xl" x-text="monthLabel"></flux:heading>

                        <flux:button.group>
                            <flux:button x-on:click="changeMonth(-1)" class="h-7! sm:h-8! px-1.5! sm:px-2!" variant="outline" size="sm">
                                <flux:icon.chevron-left icon-variant="outline" class="h-[14px] w-[14px] stroke-2" />
                            </flux:button>

                            <flux:button size="sm" x-on:click="goToToday" class="h-7! sm:h-8! px-2! sm:px-4!">
                                <span class="hidden sm:block">Today</span>

                                <flux:icon.calendar icon-variant="outline" class="sm:hidden h-4 w-4 stroke-2" />
                            </flux:button>

                            <flux:button x-on:click="changeMonth(1)" class="h-7! sm:h-8! px-1.5! sm:px-2!" variant="outline" size="sm">
                                <flux:icon.chevron-right icon-variant="outline" class="h-[14px] w-[14px] stroke-2" />
                            </flux:button>
                        </flux:button.group>
                    </div>

                    <div class="grid grid-cols-7 border-y border-zinc-200 dark:border-white/20 text-center font-medium bg-zinc-100 sm:text-sm dark:bg-zinc-800 text-xs py-1.5 sm:py-2">
                        <template x-for="(day, index) in dayNames" :key="index">
                            <div>
                                <div class="text-zinc-800 dark:text-zinc-100 text-xs sm:text-sm font-medium text-center sm:hidden" x-text="day.substring(0,1)"></div>
                                <div class="text-zinc-800 dark:text-zinc-100 text-xs sm:text-sm font-medium text-center hidden sm:block lg:hidden" x-text="day.substring(0,3)"></div>
                                <div class="text-zinc-800 dark:text-zinc-100 text-xs sm:text-sm font-medium text-center hidden lg:block" x-text="day"></div>
                            </div>
                        </template>
                    </div>

                    <div class="grid grid-cols-7 gap-y-px sm:gap-x-px sm:bg-zinc-200 sm:dark:bg-zinc-600">
                        <template x-for="(day, index) in days" :key="index">
                            <div class="p-1 max-h-[56px] sm:min-h-[140px] sm:overflow-scroll text-sm text-left flex flex-col"
                                x-on:click="!day.blank && selectDay(day)"
                                :class="{
                                    'bg-white dark:bg-zinc-900': !day.blank,
                                    'text-zinc-400 dark:text-zinc-500 bg-striped dark:bg-striped': day.blank,
                                }"
                            >
                                <div class="flex flex-col h-full">
                                    <div class="sticky mx-auto sm:mx-0 top-0 z-10 font-medium p-0.5 aspect-square flex items-center justify-center shrink-0 w-[21px] text-xs rounded-full"
                                        :class="{
                                            'bg-emerald-500 text-white': selectedDay && selectedDay.date === day.date && day.isToday,
                                            'text-emerald-500 sm:bg-emerald-500 sm:text-white': day.isToday && (!selectedDay || selectedDay.date !== day.date),
                                            'bg-zinc-700 text-white dark:bg-zinc-100 dark:text-zinc-800! sm:bg-emerald-500! sm:text-white! sm:dark:bg-emerald-500! sm:dark:text-white!': selectedDay && selectedDay.date === day.date && !day.isToday
                                        }"
                                        x-text="day.day">
                                    </div>

                                    <div class="overflow-y-auto p-1 gap-1 flex-col hidden sm:flex">
                                        <template x-for="bill in day.bills" :key="bill.id">
                                            <flux:modal.trigger
                                                x-on:click="
                                                    setCurrentMonth();
                                                    $dispatch('load-bill', { bill_id: bill.id })
                                                "
                                            >
                                                <button type="button" class="text-xs text-left px-1 py-0.5 rounded cursor-pointer"
                                                :class="{
                                                    'bg-amber-400/25 dark:bg-amber-400/40 text-amber-700 dark:text-amber-200': !bill.paid,
                                                    'bg-emerald-400/25 dark:bg-emerald-400/40 text-emerald-700 dark:text-emerald-200': bill.paid
                                                }">
                                                    <p x-text="bill.name" class="truncate font-medium"></p>
                                                </button>
                                            </flux:modal.trigger>
                                        </template>
                                    </div>

                                    <div class="flex items-center justify-center mt-1 mb-0.5 sm:hidden">
                                        <span x-cloak x-show="day.bills.length"
                                            class="min-w-1.5 min-h-1.5 aspect-square rounded-full bg-zinc-800 dark:bg-white"
                                            :class="{ 'bg-emerald-500!': day.bills.every(bill => bill.paid) }"
                                        ></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="sm:hidden grow min-h-0 p-2 border-t relative border-zinc-200 dark:border-white/20 overflow-y-auto">
                    <div class="w-full flex justify-center">
                        <div
                            class="overflow-y-auto gap-1.5 flex-col flex w-full"
                            x-show="selectedDay && selectedDay.bills.length"
                            x-cloak
                        >
                            <template x-for="bill in selectedDay?.bills" :key="bill.id">
                                <flux:modal.trigger
                                    x-on:click="
                                        setCurrentMonth();
                                        $dispatch('load-bill', { bill_id: bill.id })
                                    "
                                    class="w-full!"
                                >
                                    <button type="button" class="text-xs text-left flex items-center justify-between p-1.5 rounded-md cursor-pointer"
                                    :class="{
                                        'bg-amber-400/25 dark:bg-amber-400/40 text-amber-700 dark:text-amber-200': !bill.paid,
                                        'bg-emerald-400/25 dark:bg-emerald-400/40 text-emerald-700 dark:text-emerald-200': bill.paid
                                    }">
                                        <p x-text="bill.name" class="truncate font-medium"></p>
                                        <p x-text="'$' + bill.amount" class="truncate font-medium"></p>
                                    </button>
                                </flux:modal.trigger>
                            </template>
                        </div>

                        <p
                            class="absolute inset-0 font-medium text-sm text-center flex items-center justify-center tracking-wide"
                            x-show="!selectedDay?.bills.length"
                            x-cloak
                        >
                            No Bills
                        </p>
                    </div>
                </div>
            </x-slot:content>
        </x-card>
    @else
        <x-card dynamic-height>
            <x-slot:content>
                <div class="p-3 gap-2.5 flex items-center justify-between dark:bg-zinc-900 border-b border-zinc-200 dark:border-white/10 rounded-t-[8px]">
                    <flux:heading class="text-xl" x-text="monthLabel"></flux:heading>

                    <flux:button.group>
                        <flux:button x-on:click="changeMonth(-1)" class="h-7! sm:h-8! px-1.5! sm:px-2!" variant="outline" size="sm">
                            <flux:icon.chevron-left icon-variant="outline" class="h-[14px] w-[14px] stroke-2" />
                        </flux:button>

                        <flux:button size="sm" x-on:click="goToToday" class="h-7! sm:h-8! px-2! sm:px-4!">
                            <span class="hidden sm:block">Today</span>
                            <flux:icon.calendar icon-variant="outline" class="sm:hidden h-4 w-4 stroke-2" />
                        </flux:button>

                        <flux:button x-on:click="changeMonth(1)" class="h-7! sm:h-8! px-1.5! sm:px-2!" variant="outline" size="sm">
                            <flux:icon.chevron-right icon-variant="outline" class="h-[14px] w-[14px] stroke-2" />
                        </flux:button>
                    </flux:button.group>
                </div>

                <div class="grow min-h-0 overflow-y-auto">
                    @foreach ($bill_groups as $date => $group)
                        <section
                            wire:key="bill-date-{{ $date }}"
                            x-show="current && '{{ $date }}'.startsWith(formatDate(current).substring(0, 7))"
                            x-bind:class="{ 'border-b border-zinc-200 dark:border-white/10': currentMonthBills.at(-1)?.date !== '{{ $date }}' }"
                            class="pt-2 px-3 pb-3 sm:pt-3 sm:px-4 sm:pb-4"
                        >
                            <div class="mb-2 flex items-center gap-2">
                                <flux:heading size="sm">
                                    {{ Carbon::parse($date)->format('l, F j, Y') }}
                                </flux:heading>

                                <flux:badge
                                    x-cloak
                                    x-show="'{{ $date }}' === formatDate(today)"
                                    size="sm"
                                    variant="solid"
                                    class="py-0.5! px-1.75! rounded-[5px]! bg-zinc-800 dark:bg-white! dark:text-zinc-800!"
                                >
                                    Today
                                </flux:badge>
                            </div>

                            <div class="flex flex-col gap-2">
                                @foreach ($group as $bill)
                                    <flux:modal.trigger
                                        wire:key="bill-list-{{ $bill->id }}"
                                        x-on:click="setCurrentMonth(); $dispatch('load-bill', { bill_id: {{ $bill->id }} })"
                                    >
                                        <button
                                            type="button"
                                            @class([
                                                'flex w-full items-center justify-between gap-4 rounded-md px-2 py-1 text-left text-xs font-medium',
                                                'bg-emerald-400/25 text-emerald-700 hover:bg-emerald-400/35 dark:bg-emerald-400/40 dark:text-emerald-200 dark:hover:bg-emerald-400/50' => $bill->paid,
                                                'bg-amber-400/25 text-amber-700 hover:bg-amber-400/35 dark:bg-amber-400/40 dark:text-amber-200 dark:hover:bg-amber-400/50' => ! $bill->paid,
                                            ])
                                        >
                                            <span class="min-w-0 truncate">{{ $bill->name }}</span>
                                            <span class="shrink-0">${{ Number::format($bill->amount, 2) }}</span>
                                        </button>
                                    </flux:modal.trigger>
                                @endforeach
                            </div>
                        </section>
                    @endforeach

                    <p
                        x-cloak
                        x-show="currentMonthBills.length === 0"
                        class="p-8 text-center text-sm text-zinc-500 dark:text-zinc-400"
                    >
                        No bills this month
                    </p>
                </div>

                <div class="shrink-0 flex items-center justify-between gap-4 border-t border-zinc-200 p-3 px-4 text-sm font-medium dark:border-white/10">
                    <span>Total</span>
                    <span x-cloak x-text="formatAmount(currentMonthTotal)">${{ Number::format($bill_total, 2) }}</span>
                </div>
            </x-slot:content>
        </x-card>
    @endif

    <livewire:bill-form />
</div>

@script
    <script>
        Alpine.data('calendar', () => {
            return {
                today: new Date(),
                current: null,
                selectedDay: null,
                preferredDay: null,
                dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                days: [],
                monthLabel: '',
                bills: @js($bills),

                init() {
                    this.preferredDay = this.today.getDate();

                    if (storedMonth = sessionStorage.getItem('calendarMonth')) {
                        const [year, month] = storedMonth.split('-').map(Number);

                        this.current = this.getMonthStart(new Date(year, month - 1, 1));

                        sessionStorage.removeItem('calendarMonth');
                    } else {
                        this.current = this.getMonthStart(new Date());
                    }

                    this.refresh();
                },

                goToToday() {
                    this.preferredDay = this.today.getDate();
                    this.current = this.getMonthStart(this.today);
                    this.refresh();
                },

                formatDate(date) {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                },

                changeMonth(offset) {
                    this.current = this.getMonthStart(new Date(this.current.getFullYear(), this.current.getMonth() + offset, 1));
                    this.refresh();

                    this.changeDefaultDate(this.selectedDay.date);
                },

                refresh() {                    
                    this.monthLabel = this.current.toLocaleDateString('default', {
                        month: 'long',
                        year: 'numeric',
                    });

                    this.days = this.generateDays();

                    const lastDay = new Date(
                        this.current.getFullYear(),
                        this.current.getMonth() + 1,
                        0,
                    ).getDate();
                    const selectedDayNumber = Math.min(this.preferredDay, lastDay);

                    this.selectedDay = this.days.find(day =>
                        !day.blank && day.day === selectedDayNumber
                    ) ?? null;
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
                            bills: this.bills.filter(b => b.date === dateStr),
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
                },

                changeDefaultDate(date) {
                    this.$dispatch('set-default-date', { date });
                },

                selectDay(day) {
                    if (window.matchMedia('(min-width: 640px)').matches) {
                        return;
                    }

                    this.selectedDay = day;
                    this.preferredDay = day.day;
                    this.changeDefaultDate(day.date);
                },

                get currentMonthBills() {
                    if (!this.current) {
                        return [];
                    }

                    const month = this.formatDate(this.current).substring(0, 7);

                    return this.bills.filter(bill => bill.date.startsWith(month));
                },

                get currentMonthTotal() {
                    return this.currentMonthBills.reduce((total, bill) => total + Number(bill.amount), 0);
                },

                get currentMonthBillCount() {
                    return this.currentMonthBills.length;
                },

                get currentMonthPaidBills() {
                    return this.currentMonthBills.filter(bill => bill.paid);
                },

                get currentMonthPaidCount() {
                    return this.currentMonthPaidBills.length;
                },

                get currentMonthPaidTotal() {
                    return this.currentMonthPaidBills.reduce((total, bill) => total + Number(bill.amount), 0);
                },

                get currentMonthUnpaidCount() {
                    return this.currentMonthBillCount - this.currentMonthPaidCount;
                },

                get currentMonthUnpaidTotal() {
                    return this.currentMonthTotal - this.currentMonthPaidTotal;
                },

                billCountLabel(count) {
                    return `${count} ${count === 1 ? 'bill' : 'bills'}`;
                },

                formatAmount(amount) {
                    return new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'USD',
                    }).format(amount);
                },

                setCurrentMonth() {
                    sessionStorage.setItem('calendarMonth', this.formatDate(this.current).substring(0, 7));
                }
            };
        });
    </script>
@endscript
