@props(['name', 'heading'])

<div>
    <flux:modal :name="$name" class="sm:!w-1/3">
        <flux:heading size="lg" class="font-semibold -mt-1.5!">
            Delete {{ Str::headline($heading) }}?
        </flux:heading>

        <flux:text class="mt-2 flex flex-wrap">
            <p>
                You're about to delete this {{ $heading }}.
                This action cannot be reversed.
            </p>
        </flux:text>

        <div class="flex mt-6 gap-2">
            <flux:spacer />

            <flux:modal.close>
                <flux:button variant="ghost" size="sm">
                    Cancel
                </flux:button>
            </flux:modal.close>

            <flux:button type="button" wire:click='delete' variant="danger" size="sm">
                Delete
            </flux:button>
        </div>
    </flux:modal>
</div>
