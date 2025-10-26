@use('Livewire\Features\SupportFileUploads\TemporaryUploadedFile', 'TemporaryUploadedFile')

<div x-data="cropper" class="flex w-fit flex-col">
    <h4 class="text-sm font-medium select-none text-zinc-800 dark:text-white">
        Avatar
    </h4>

    <div class="flex items-center space-x-3">
        <label for="avatar"
            class="relative @if ($avatar) cursor-normal! @else cursor-pointer @endif">
            @if (!$avatar)
                <flux:input type="file" wire:model="avatar" class="sr-only!" id="avatar" />
            @endif

            @if ($avatar)
                <img src="{{ $this->avatarUrl() }}" alt="Avatar" class="rounded-xl size-24 mt-2" id="avatar" />
            @else
                <div
                    class="flex items-center justify-center bg-zinc-100 hover:bg-zinc-200 duration-100 ease-in-out rounded-xl border dark:border-white/10 dark:bg-white/10 dark:hover:bg-zinc-800 size-24 mt-2">
                    <svg wire:loading.remove wire:target="avatar" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                    </svg>

                    <flux:icon.loading wire:loading wire:target="avatar" />
                </div>
            @endif
        </label>

        @if ($avatar)
            <flux:button variant="outline" type="button" wire:click="removeAvatar" size="sm" class="mt-1.5">
                Remove
            </flux:button>
        @endif
    </div>

    <template x-cloak x-if="$wire.avatar">
        <flux:modal wire:model.self='show_crop_avatar_modal' :dismissible="false" :closable="false">
            <form x-on:submit.prevent='saveCroppedImage' class="space-y-6">
                <flux:heading size="lg" class="font-semibold -mt-1.5!">
                    Crop Avatar
                </flux:heading>

                <div>
                    <img id="crop-avatar" src="{{ $this->avatarUrl() }}" alt="Avatar" class="w-full max-w-full" />
                </div>             

                <div class="flex gap-2">
                    <flux:spacer />

                    <flux:modal.close>
                        <flux:button  variant="ghost" size="sm">
                            Cancel
                        </flux:button>
                    </flux:modal.close>

                    <flux:button type="submit" wire:loading.attr='disabled' wire:target='save' variant="primary" size="sm">
                        Save
                    </flux:button>
                </div>
            </form>
        </flux:modal>
    </template>

    <flux:error name="avatar" />
</div>

@script
    <script>
        Alpine.data('cropper', () => {
            return {
                cropper: null,
                cropRegion: null,

                saveCroppedImage() {
                    this.$wire.save(cropRegion);
                },

                init () {
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape' && this.$wire.show_crop_avatar_modal) {
                            e.stopPropagation();
                            e.preventDefault();
                        }
                    });

                    this.$wire.$watch('show_crop_avatar_modal', () => {                        
                        this.$nextTick(() => {
                            cropper = new Cropper(document.getElementById('crop-avatar'), {
                                autoCropArea: 1,
                                viewMode: 1,
                                aspectRatio: 1/1,

                                crop (e) {
                                    cropRegion = {
                                        x: e.detail.x,
                                        y: e.detail.y,
                                        width: e.detail.width,
                                        height: e.detail.height,
                                    };
                                }
                            });
                        });
                    });
                }
            };
        });
    </script>
@endscript