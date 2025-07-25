<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-900">
    <flux:sidebar sticky stashable class="border-r border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="mr-5 flex items-center space-x-2" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group class="grid">
                <flux:navlist.item icon="layout-dashboard" :href="route('dashboard')"
                    :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:navlist.item>

                <flux:navlist.item icon="user" :href="route('accounts')"
                    :current="request()->routeIs('accounts')" wire:navigate>
                    {{ __('Accounts') }}
                </flux:navlist.item>

                <flux:navlist.item icon="currency-dollar" :href="route('planned-spending')"
                    :current="request()->routeIs('planned-spending')" wire:navigate>
                    {{ __('Planned Spending') }}
                </flux:navlist.item>

                <flux:navlist.item icon="target" :href="route('savings-goals')"
                    :current="request()->routeIs('savings-goals')" wire:navigate>
                    {{ __('Savings Goals') }}
                </flux:navlist.item>

                <flux:navlist.item icon="calendar-days" :href="route('bill-calendar')"
                    :current="request()->routeIs('bill-calendar')" wire:navigate>
                    {{ __('Bill Calendar') }}
                </flux:navlist.item>

                <flux:navlist.item icon="scroll-text" :href="route('transactions')"
                    :current="request()->routeIs('transactions')" wire:navigate>
                    {{ __('Transactions') }}
                </flux:navlist.item>

                <flux:navlist.item icon="queue-list" :href="route('categories')"
                    :current="request()->routeIs('categories')" wire:navigate>
                    {{ __('Categories') }}
                </flux:navlist.item>

                <flux:navlist.item icon="tags" :href="route('tags')"
                    :current="request()->routeIs('tags')" wire:navigate>
                    {{ __('Tags') }}
                </flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />

        <!-- Desktop User Menu -->
        <flux:dropdown position="bottom" align="start">
            @if (auth()->user()->avatar)
                <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
                    :avatar="Storage::disk('s3')->url('avatars/' . auth()->user()->avatar)"
                    icon-trailing="chevrons-up-down" />
            @else
                <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
                    icon-trailing="chevrons-up-down" />
            @endif

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <div
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    @if (auth()->user()->avatar)
                                        <img
                                            src="{{ Storage::disk('s3')->url('avatars/' . auth()->user()->avatar) }}" />
                                    @else
                                        <p>{{ auth()->user()->initials() }}</p>
                                    @endif
                                </div>
                            </span>

                            <div class="grid flex-1 text-left text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:radio.group x-data variant="segmented" size="sm" x-model="$flux.appearance">
                    <flux:radio value="light" icon="sun" />
                    <flux:radio value="dark" icon="moon" />
                    <flux:radio value="system" icon="computer-desktop" />
                </flux:radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item href="/settings/profile" icon="cog" wire:navigate>{{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden" x-data x-ref="mobileHeader" x-init="window.mobileHeaderHeight = $el.offsetHeight">
        <flux:sidebar.toggle class="lg:hidden text-zinc-800! dark:text-zinc-200!" icon="panel-left" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            @if (auth()->user()->avatar)
                <flux:profile :initials="auth()->user()->initials()"
                    :avatar="Storage::disk('s3')->url('avatars/' . auth()->user()->avatar)"
                    icon-trailing="chevrons-up-down" />
            @else
                <flux:profile :initials="auth()->user()->initials()"
                    icon-trailing="chevrons-up-down" />
            @endif

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    @if (auth()->user()->avatar)
                                        <img
                                            src="{{ Storage::disk('s3')->url('avatars/' . auth()->user()->avatar) }}" />
                                    @else
                                        <p>{{ auth()->user()->initials() }}</p>
                                    @endif
                                </span>
                            </span>

                            <div class="grid flex-1 text-left text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:radio.group x-data variant="segmented" size="sm" x-model="$flux.appearance">
                    <flux:radio value="light" icon="sun" />
                    <flux:radio value="dark" icon="moon" />
                    <flux:radio value="system" icon="computer-desktop" />
                </flux:radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item href="/settings/profile" icon="cog" wire:navigate>Settings</flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
                </flux:menu.item>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @persist('toast')
        <flux:toast />
    @endpersist
    
    @fluxScripts
</body>

</html>
