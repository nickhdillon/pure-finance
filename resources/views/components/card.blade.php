@props(['heading' => null, 'button' => null, 'content'])

<flux:card class="bg-zinc-50/80 dark:bg-zinc-800!">
    @if ($heading) 
        <div class="flex w-full items-center px-4 py-3 justify-between">
            <flux:heading class="text-xl">
                {{ $heading }}
            </flux:heading>
    
            @if ($button) 
                {{ $button }}
            @endif
        </div>
    @endif

    <div {{ $attributes->merge(['class' => 'flex flex-col border border-zinc-200 bg-white dark:bg-zinc-900 dark:border-zinc-700 m-[5px] rounded-[8px] inset-shadow-xs']) }}>
        {{ $content }}
    </div>
</flux:card>