<div>
    <x-card>
        <x-slot:content>
            <div
                x-data="{
                    current: 0,
                    components: ['monthly-spending-overview', 'net-worth'],
                    touchStartX: 0,

                    handleTouchStart(e) {
                        this.touchStartX = e.touches[0].clientX;
                    },

                    handleTouchEnd(e) {
                        const diff = e.changedTouches[0].clientX - this.touchStartX;
                        if (Math.abs(diff) > 50) diff > 0 ? this.prev() : this.next();
                    },

                    prev() {
                        this.current = Math.max(0, this.current - 1);
                    },

                    next() {
                        this.current = Math.min(this.components.length - 1, this.current + 1);
                    }
                }"
                x-on:touchstart="handleTouchStart"
                x-on:touchend="handleTouchEnd"
                class="relative overflow-hidden w-full select-none"
            >
                <div 
                    class="flex transition-transform duration-300 ease-in-out"
                    :style="`transform: translateX(-${current * 100}%)`"
                >
                    <div class="w-full flex-shrink-0">
                        <livewire:monthly-spending-overview />
                    </div>

                    <div class="w-full flex-shrink-0">
                        <livewire:net-worth />
                    </div>
                </div>

                <div class="absolute bottom-3 sm:bottom-2 left-0 right-0 flex justify-center items-center gap-3">
                    <flux:button
                        icon="chevron-left"
                        variant="ghost"
                        size="sm"
                        class="hidden! sm:flex!"
                        x-on:click="prev()"
                        x-show="current > 0"
                        x-bind:disabled="current === 0"
                    />

                    <template x-for="(component, index) in components" :key="index">
                        <button
                            class="size-2 rounded-full transition sm:hidden"
                            :class="current === index ? 'bg-emerald-500' : 'bg-zinc-400 dark:bg-zinc-600'"
                            x-on:click="current = index"
                        ></button>
                    </template>

                    <flux:button
                        icon="chevron-right"
                        variant="ghost"
                        size="sm"
                        class="hidden! sm:flex!"
                        x-on:click="next()"
                        x-bind:disabled="current === components.length - 1"
                    />
                </div>
            </div>
        </x-slot:content>
    </x-card>
</div>
