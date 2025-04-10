@use('App\Services\S3Service', 'S3Service')

<div>
    <flux:modal wire:model.self='show_attachments' name="attachments" x-on:close="$wire.resetAttachments()">
        <div wire:loading.remove class="space-y-6">
            <div class="space-y-6">
                <flux:heading size="lg" class="font-semibold -mt-1.5!">
                    Attachments
                </flux:heading>

                <div x-cloak wire:loading.remove wire:target='loadAttachments'
                class="flex flex-col justify-center space-y-5">
                    @foreach ($attachments as $attachment)
                        <img src="{{ S3Service::getS3Path($attachment['name']) }}" alt="{{ $attachment['name'] }}"
                            class="rounded-lg max-h-[550px]" />
                    @endforeach
                </div>
            </div>
        </div>

        <div x-cloak wire:loading.flex class="flex items-center justify-center w-full h-[500px]">
            <flux:icon.loading />
        </div>
    </flux:modal>
</div>
