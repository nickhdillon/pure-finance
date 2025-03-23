<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main class="mx-auto w-full max-w-6xl">
        {{ $slot }}
    </flux:main>
</x-layouts.app.sidebar>
