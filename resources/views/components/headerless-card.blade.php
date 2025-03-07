<flux:card>
    <div {{ $attributes->merge(['class' => 'flex flex-col border border-zinc-200 bg-white dark:bg-zinc-800 dark:border-zinc-700 m-[5px] rounded-[8px] shadow-xs']) }}>
        {{ $slot }}
    </div>
</flux:card>