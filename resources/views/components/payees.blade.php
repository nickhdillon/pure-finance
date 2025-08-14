@props(['user_payees'])

<flux:field>
    <flux:label>
        Payees
    </flux:label>

    <flux:select variant="listbox" searchable multiple placeholder="Select payees" clearable x-model="$wire.payees">
        <x-slot name="search">
            <flux:select.search class="px-4" placeholder="Search payees..." />
        </x-slot>
        
        @foreach ($user_payees as $payee)
            <flux:select.option value="{!! $payee !!}" class="font-semibold">
                {!! $payee !!}
            </flux:select.option>
        @endforeach
    </flux:select>

    <flux:error name="payees" />

    <div x-cloak x-show="$wire.payees.length" class="flex flex-wrap gap-1.5">
        <template x-for="(payee, index) in $wire.payees" :key="index">
            <flux:badge color="emerald">
                <p x-text="payee"></p> 
                <flux:badge.close x-on:click="$wire.payees.splice(index, 1)" />
            </flux:badge>
        </template>
    </div>
</flux:field>
