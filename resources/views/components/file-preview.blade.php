@props(['file'])
@use('App\Services\S3Service', 'S3Service')

<div>
    <flux:modal.trigger name="file-preview-{{ $file['name'] }}">
        <img src="{{ S3Service::getS3Path($file['name']) }}" alt="{{ $file['original_name'] }}"
            class="w-8 h-8 rounded-md" />
    </flux:modal.trigger>

    <flux:modal name="file-preview-{{ $file['name'] }}" class="max-h-full overflow-auto">
        <div class="space-y-6">
            <flux:heading size="lg" class="font-semibold -mt-1.5!">
                {{ $file['original_name'] }}
            </flux:heading>

            <div class="flex justify-center">
                <img src="{{ S3Service::getS3Path($file['name']) }}" alt="{{ $file['original_name'] }}"
                    class="rounded-md max-h-[550px]" />
            </div>
        </div>
    </flux:modal>
</div>