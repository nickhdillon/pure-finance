<div>
    <flux:modal name="bill-details" variant="flyout" class="w-[325px]!">
        <div class="space-y-6">
            <flux:heading size="lg">Bill Details</flux:heading>

            <form class="space-y-6">
                <flux:input label="Name" placeholder="Your name" />
                <flux:input label="Date of birth" type="date" />
                <div class="flex">
                    <flux:spacer />
                    <flux:button type="submit" variant="primary" class="w-full">
                        Save changes
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
