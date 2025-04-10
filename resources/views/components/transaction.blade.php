@props(['transaction'])

@use('App\Enums\TransactionType', 'TransactionType')

<div wire:key='{{ $transaction->id }}' x-data="transaction" x-on:transaction-deleted.window="resetSwipe"
    x-on:status-changed.window="resetSwipe" x-on:click.outside="resetSwipe"
    class="relative overflow-hidden">
    <div x-show="leftSwipe" x-transition:enter="transform duration-200" x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0" x-transition:leave="transform duration-200"
        x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
        @class([
            'absolute top-0 bottom-0 right-0 flex items-center text-white border',
            'bg-emerald-400 border-emerald-500 dark:bg-emerald-600/80' => !$transaction->status,
            'bg-amber-400 border-amber-500 dark:bg-amber-600/80' =>
                $transaction->status,
        ])>
        <flux:button class="!p-0 hover:!bg-transparent" variant="ghost"
            wire:click="toggleStatus({{ $transaction->id }})">
            @if ($transaction->status)
                <flux:icon.clock-alert class="w-[44px] p-1 ease-in-out rounded-md text-amber-500 h-7" />
            @else
                <flux:icon.check-circle class="w-[44px] p-1 ease-in-out rounded-md text-emerald-500 h-8" />
            @endif
        </flux:button>
    </div>

    <a href="{{ route('edit-transaction-form', $transaction->id) }}" @class([
        'flex flex-col p-3 py-2.5 text-sm bg-white dark:bg-zinc-900 transform transition-transform duration-300',
        'border-r-2 !border-r-emerald-500' => $transaction->status === true,
        'border-r-2 !border-r-amber-500' => $transaction->status === false,
    ])
        x-bind:style="contentStyle">
        <div class="flex items-center justify-between font-medium">
            <p class="text-zinc-700 truncate max-w-[215px] dark:text-zinc-200">
                {{ $transaction->payee }}
            </p>

            <div class="flex items-center">
                @if ($transaction->attachments) 
                    <flux:modal.trigger name="attachments">
                        <div x-data="{ attachments: @js($transaction->attachments) }">
                            <flux:icon.photo
                                class="!text-zinc-600 !h-5 mr-1.5 dark:!text-zinc-100"
                                x-on:click.prevent="$dispatch('load-attachments', { attachments })"
                            />
                        </div>
                    </flux:modal.trigger>
                @endif

                <div class="flex items-center">
                    @if (in_array($transaction->type, [TransactionType::DEBIT, TransactionType::TRANSFER, TransactionType::WITHDRAWAL]))
                        <span class="text-zinc-700">-</span>
                    @else
                        <span class="text-emerald-500">+</span>
                    @endif

                    <span @class([
                        '!text-emerald-500' => in_array($transaction->type, [
                            TransactionType::CREDIT,
                            TransactionType::DEPOSIT,
                        ]),
                        'text-zinc-700 dark:text-zinc-200'
                    ])>
                        ${{ Number::format($transaction->amount ?? 0, 2) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-300">
            <div class="flex items-center">
                <p class="max-w-[215px] truncate">
                    @if ($transaction->category->parent)
                        {{ $transaction->category->parent->name }} &rarr; {{ $transaction->category->name }}
                    @else
                        {{ $transaction->category->name }}
                    @endif
                </p>
            </div>

            <p>
                {{ Carbon\Carbon::parse($transaction->date)->format('M j, Y') }}
            </p>
        </div>

        @if ($transaction->tags)
            <div class="text-zinc-500 dark:text-zinc-300">
                {{ $transaction->tags->pluck('name')->implode(', ') }}
            </div>
        @endif
    </a>
</div>

@script
    <script>
        Alpine.data('transaction', () => {
            return {
                leftSwipe: false,
                startX: 0,
                currentX: 0,

                get contentStyle() {
                    return `transform: translateX(${
                        this.leftSwipe ? '-44px' : '0px'
                    })`;
                },

                resetSwipe() {
                    this.leftSwipe = false;
                },

                handleTouchStart(event) {
                    this.startX = event.touches[0].clientX;
                    this.currentX = this.startX;
                },

                handleTouchMove(event) {
                    this.currentX = event.touches[0].clientX;
                    const swipeDistance = 50;

                    if (this.startX - this.currentX > swipeDistance) {
                        this.leftSwipe = true;
                    }
                },

                init() {
                    this.$el.addEventListener('touchstart', this.handleTouchStart.bind(this));
                    this.$el.addEventListener('touchmove', this.handleTouchMove.bind(this));
                }
            };
        })
    </script>
@endscript
