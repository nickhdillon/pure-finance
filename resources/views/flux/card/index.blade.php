@php
$classes = Flux::classes()
    ->add('rounded-[12px]')
    ->add('bg-zinc-50/80 dark:bg-zinc-900')
    ->add('border border-zinc-200 dark:border-zinc-700')
    ->add('-space-y-1')
    ->add('shadow-xs dark:shadow-lg')
@endphp

<div {{ $attributes->class($classes) }} data-flux-card>
    {{ $slot }}
</div>
