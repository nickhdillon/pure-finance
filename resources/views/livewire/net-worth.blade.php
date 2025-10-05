<div class="flex flex-col space-y-6 p-3 w-full h-full">
    <div>
        <flux:heading class="text-xl">
            Net Worth
        </flux:heading>

        <flux:heading class="text-lg">
            ${{ $this->bankingTotal - $this->debtTotal + $this->investmentTotal }}
        </flux:heading>
    </div>

    <div class="flex flex-col justify-center items-center flex-1 space-y-5 w-full">
        <div class="flex items-center w-full justify-between">
            <flux:text variant="strong" class="font-medium">Banking</flux:text>
            <flux:text variant="strong" class="font-medium">${{ $this->bankingTotal }}</flux:text>
        </div>

        <div class="flex items-center w-full justify-between">
            <flux:text variant="strong" class="font-medium">Debt</flux:text>
            <flux:text variant="strong" class="font-medium">${{ $this->debtTotal }}</flux:text>
        </div>

        <div class="flex items-center w-full justify-between">
            <flux:text variant="strong" class="font-medium">Investments</flux:text>
            <flux:text variant="strong" class="font-medium">${{ $this->investmentTotal }}</flux:text>
        </div>
    </div>
</div>
