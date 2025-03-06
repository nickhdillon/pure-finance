@props(['heading', 'button' => null, 'content'])

<flux:card>
    <div class="flex w-full items-center px-4 py-3 justify-between">
        <flux:heading class="font-semibold text-xl">
            {{ $heading }}
        </flux:heading>

        @if ($button) 
            {{ $button }}
        @endif
    </div>

    <div {{ $attributes->merge(['class' => 'flex flex-col border border-zinc-200 bg-white dark:bg-zinc-800 dark:border-zinc-700 m-[5px] mt-1 rounded-[8px] inset-shadow-xs']) }}>
        {{ $content }}
    </div>
</flux:card>