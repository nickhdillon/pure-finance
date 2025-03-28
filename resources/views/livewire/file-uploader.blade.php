<div>
    <flux:label class="hidden" />

    <div x-data="{
        isDragging: false,
        handleDrop(e) {
            e.preventDefault()
            this.isDragging = false
            this.$refs.input.files = e.dataTransfer.files
            this.$refs.input.dispatchEvent(new Event('change'))
        }
    }" x-on:dragenter.prevent="isDragging = true" x-on:dragleave.prevent="isDragging = false"
        x-on:dragover.prevent x-on:drop="handleDrop($event)" class="relative">
        <label for="files"
            @disabled($disabled)
            @class([
                'flex flex-col items-center justify-center w-full h-48 transition-colors border border-dashed rounded-lg cursor-pointer bg-white inset-shadow-xs dark:bg-zinc-900 border-zinc-200 dark:border-zinc-700 dark:hover:border-emerald-600 hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-950/50',
                'cursor-default! opacity-50 hover:border-zinc-200 dark:hover:border-zinc-500 hover:bg-white dark:hover:bg-zinc-900' => $disabled
            ])
            :class="{
                'border-zinc-300 dark:border-zinc-700': !isDragging,
                '!border-emerald-500 !bg-emerald-50 dark:!bg-emerald-950/50': isDragging
            }">
            <flux:icon.arrow-up-to-line wire:target='files' wire:loading.remove
                class="!h-6 !w-6 !mb-3 !text-zinc-400" />

            <div class="flex items-center justify-center mb-4 space-x-1.5 text-sm italic font-medium text-zinc-500"
                wire:target='files' wire:loading.flex>
                <span>Uploading files</span>

                <flux:icon.loading wire:target='files' wire:loading class="h-[16px]!" />
            </div>

            <p class="mb-2 text-sm text-zinc-500 dark:text-zinc-400">
                <span class="font-semibold text-emerald-500">
                    Click to upload
                </span>

                or drag and drop
            </p>

            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                JPG, JPEG, PNG, HEIC, SVG, AVIF, or WEBP
            </p>

            <input id="files" name="files" type="file" x-ref="input" wire:model="files"
                accept=".jpg, .jpeg, .png, .heic, .svg, .avif, .webp" class="hidden cursor-pointer" multiple />
        </label>

        @if ($uploaded_files)
            <div class="space-y-2.5">
                @foreach ($uploaded_files->reverse() as $file)
                    <div wire:key="{{ $file['name'] }}-{{ $file['size'] }}" wire:transition.duration.300ms
                        class="flex items-center shadow-xs mt-2.5 justify-between p-[7px] border border-zinc-200 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-900">
                        <div class="flex items-center space-x-2">
                            <div>
                                <x-file-preview :$file />
                            </div>

                            <div class="flex flex-col -space-y-1">
                                <p class="text-[13px] text-zinc-600 dark:text-zinc-300">
                                    {{ $file['name'] }}
                                </p>

                                <span class="text-xs text-zinc-400">
                                    Size: {{ $file['size'] }}
                                </span>
                            </div>
                        </div>

                        <flux:button icon="x-mark" variant="ghost" class="h-7! w-7! text-red-500! rounded-md" type="button" wire:click="removeFile('{{ $file['name'] }}')" />
                    </div>
                @endforeach
            </div>
        @endif

        @error('files.*')
            <div class="pt-2 text-sm text-rose-600 dark:text-rose-400">
                {{ $message }}
            </div>
        @enderror
    </div>
</div>