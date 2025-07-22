<?php

use App\Models\User;
use Livewire\Volt\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public array $routes = [];

    public string $name = '';

    public string $email = '';

    public string $preferred_homepage = '';

    public array $phone_numbers = [];

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore(Auth::id())],
            'preferred_homepage' => ['nullable', 'string'],
            'phone_numbers' => ['nullable', 'array'],
            'phone_numbers.*.value' => ['nullable', 'phone:US'],
        ];
    }

    protected function messages(): array
    {
        return [
            'phone_numbers.*.value.phone' => 'The phone number must be a valid number'
        ];
    }

    public function mount(): void
    {
        $this->routes = [
            'dashboard' => 'Dashboard',
            'accounts' => 'Accounts',
            'planned-spending' => 'Planned Spending',
            'transactions' => 'Transactions',
            'categories' => 'Categories',
            'tags' => 'Tags',
        ];

        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->preferred_homepage = $user->preferred_homepage ?? 'dashboard';
        $this->phone_numbers = $user->phone_numbers ?? [];
    }

    public function updateProfileInformation(): void
    {
        $this->validate();

        $filtered_phone_numbers = array_values(
            array_filter($this->phone_numbers, fn(array $number): bool => !empty($number['value']))
        );

        auth()
            ->user()
            ->update([
                'name' => $this->name,
                'email' => $this->email,
                'preferred_homepage' => $this->preferred_homepage,
                'phone_numbers' => $filtered_phone_numbers,
            ]);

        $this->dispatch('profile-updated', name: $this->name);
    }
}; ?>

<section x-data="profile" class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your avatar, name, and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            @livewire('avatar')

            <flux:field>
                <flux:label>Name</flux:label>

                <flux:input type="text" wire:model='name' required />

                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>Email</flux:label>

                <flux:input type="email" wire:model='email' required />

                <flux:error name="email" />
            </flux:field>

            <flux:field>
                <flux:label>Preferred Homepage</flux:label>

                <flux:select variant="listbox" placeholder="Select a page" wire:model="preferred_homepage">
                    @foreach ($routes as $key => $value)
                    <flux:select.option value="{{ $key }}">
                        {{ $value }}
                    </flux:select.option>
                    @endforeach
                </flux:select>

                <flux:error name="preferred_homepage" />
            </flux:field>

            <flux:field>
                <flux:label>Phone Numbers</flux:label>

                <template x-for="(phone_number, index) in phoneNumbers">
                    <div class="flex items-center gap-1.5 first-of-type:mt-1">
                        <flux:input type="text" x-model='phone_number.value' />

                        <flux:button
                            x-cloak
                            x-show="phoneNumbers.length > 1"
                            icon="trash"
                            variant="outline"
                            class="text-red-500! h-[38px]! dark:bg-white/10! dark:hover:bg-zinc-700!"
                            x-on:click="deletePhoneNumber(index)" />
                    </div>
                </template>

                <flux:button
                    icon="plus"
                    variant="primary"
                    x-on:click="addPhoneNumber()"
                    size="sm"
                    class="w-18">
                    Add
                </flux:button>

                <flux:error name="phone_numbers" />

                <flux:error name="phone_numbers.*.value" />
            </flux:field>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" size="sm" class="w-full">
                        {{ __('Save') }}
                    </flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>

@script
<script>
    Alpine.data('profile', () => {
        return {
            phoneNumbers: $wire.entangle('phone_numbers'),

            addPhoneNumber() {
                this.phoneNumbers.push({
                    value: ''
                });
            },

            deletePhoneNumber(index) {
                this.phoneNumbers.splice(index, 1);
            },

            init() {
                if (this.phoneNumbers.length === 0) this.addPhoneNumber();
            }
        };
    });
</script>
@endscript