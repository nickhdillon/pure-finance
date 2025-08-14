@props(['transaction'])

@use('App\Enums\TransactionType', 'TransactionType')

<div wire:key='{{ $transaction->id }}'>
    <a class="flex flex-col p-3 py-2.5 text-sm bg-white dark:bg-zinc-900">
        <div class="flex items-center justify-between font-medium">
            <p class="text-zinc-700 truncate max-w-[215px] dark:text-zinc-200">
                {{ $transaction->payee }}
            </p>

            <div class="flex items-center">
                <div class="flex items-center">
                    @if (in_array($transaction->type, [TransactionType::DEBIT, TransactionType::TRANSFER, TransactionType::WITHDRAWAL]))
                        <span class="text-zinc-700 dark:text-zinc-200">-</span>
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
                <p class="max-w-[185px] truncate">
                    @if ($transaction->category->parent)
                        {{ $transaction->category->parent->name }} &rarr; {{ $transaction->category->name }}
                    @else
                        {{ $transaction->category->name }}
                    @endif
                </p>
            </div>

            <p>
                {{ $transaction->date->format('M j, Y') }}
            </p>
        </div>

        @if ($transaction->snapshot['tags'])
            <div class="text-zinc-500 dark:text-zinc-300">
                {{ collect($transaction->snapshot['tags'])->pluck('name')->implode(', ') }}
            </div>
        @endif
    </a>
</div>
