@props(['heading' => null, 'button' => null, 'content'])

<flux:card
    x-data="{ headerHeight: 0, dynamicHeight: null }"
    x-init="
        const shouldApplyDynamicHeight = $el.hasAttribute('dynamic-height');
        
        if (shouldApplyDynamicHeight) {
            headerHeight = window.mobileHeaderHeight + 166;

            const updateHeight = () => {
                dynamicHeight = window.matchMedia('(max-width: 640px)').matches
                    ? 'height: calc(100dvh - ' + headerHeight + 'px) !important'
                    : null;
            };

            updateHeight();
            window.addEventListener('resize', updateHeight);
        }
    "
    x-bind:style="dynamicHeight"
    {{ $attributes->merge(['class' => 'flex flex-col bg-zinc-50/80 dark:bg-zinc-800!']) }}
>
    @if ($heading)
        <div class="flex w-full items-center px-4 py-3 justify-between shrink-0">
            <flux:heading class="text-xl">
                {!! $heading !!}
            </flux:heading>

            @if ($button)
                {{ $button }}
            @endif
        </div>
    @endif

    <div @class([
        'flex flex-col grow min-h-0 bg-white dark:bg-zinc-900 m-[5px] rounded-[8px] inset-shadow-xs overflow-hidden',
        'border border-zinc-200 dark:border-zinc-700' => !$attributes->has('no-content-border'),
    ])>
        {{ $content }}
    </div>
</flux:card>