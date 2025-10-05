<div class="flex flex-col space-y-6 p-3 w-full">
    <div>
        <flux:heading class="text-xl">
            Net Worth
        </flux:heading>

        <flux:heading class="text-lg">
            ${{ $this->bankingTotal - $this->debtTotal + $this->investmentTotal }}
        </flux:heading>
    </div>

    <div class="flex flex-col space-y-5 w-full">
        <div class="flex items-center w-full justify-between">
            <flux:text class="text-zinc-800!">Banking</flux:text>
            <flux:text class="text-zinc-800!">${{ $this->bankingTotal }}</flux:text>
        </div>

        <div class="flex items-center w-full justify-between">
            <flux:text class="text-zinc-800!">Debt</flux:text>
            <flux:text class="text-zinc-800!">${{ $this->debtTotal }}</flux:text>
        </div>

        <div class="flex items-center w-full justify-between">
            <flux:text class="text-zinc-800!">Investments</flux:text>
            <flux:text class="text-zinc-800!">${{ $this->investmentTotal }}</flux:text>
        </div>
    </div>
</div>
