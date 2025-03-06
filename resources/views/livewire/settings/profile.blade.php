<?php

use App\Models\User;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

new class extends Component {
    use WithFileUploads;

    public string $name = '';

    public string $email = '';

    public TemporaryUploadedFile|string|null $avatar = null;

    public string $s3_path = 'avatars';

    protected function messages(): array
    {
        return [
            'avatar.image' => 'Avatar must be a valid image',
            'avatar.max' => 'Avatar must be less than 2MB',
            'avatar.mimes' => 'Avatar must be of type: jpg, jpeg, png',
        ];
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore(Auth::id())],
            'avatar' => ['nullable', 'image', 'max:190000', 'mimes:jpg,jpeg,png'],
        ];
    }

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->avatar = $this->getAvatarUrl($user->avatar);
    }

    private function getAvatarUrl(?string $filename): string
    {
        return $filename && Storage::disk('s3')->exists("avatars/{$filename}") ? Storage::disk('s3')->url("avatars/{$filename}") : '';
    }

    public function updatedAvatar(): void
    {
        $this->validateOnly('avatar');

        /** @var TemporaryUploadedFile $avatar */
        $avatar = $this->avatar;

        $filename = $avatar->getClientOriginalName();

        $avatar->storePubliclyAs($this->s3_path, $filename, 's3');

        auth()
            ->user()
            ->update(['avatar' => $filename]);

        $this->redirectRoute('settings.profile', navigate: true);
    }

    public function removeAvatar(): void
    {
        $user = Auth::user();

        if ($user->avatar && Storage::disk('s3')->exists("avatars/{$user->avatar}")) {
            Storage::disk('s3')->delete("avatars/{$user->avatar}");
        }

        $user->update(['avatar' => null]);

        $this->redirectRoute('settings.profile', navigate: true);
    }

    public function updateProfileInformation(): void
    {
        $this->validate();

        auth()
            ->user()
            ->update([
                'name' => $this->name,
                'email' => $this->email,
            ]);

        $this->dispatch('profile-updated', name: $this->name);
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your avatar, name, and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <div class="w-fit flex flex-col">
                <h4 class="text-sm font-medium select-none text-zinc-800 dark:text-white">
                    Avatar
                </h4>

                <div class="flex items-center space-x-3">
                    <label for="avatar"
                        class="relative @if ($avatar) cursor-normal! @else cursor-pointer @endif">
                        @if (!$avatar)
                        <flux:input type="file" wire:model="avatar" class="hidden" id="avatar" />
                        @endif

                        @if ($avatar)
                        <img src="{{ $avatar }}" alt="Avatar" class="rounded-full size-24 mt-2" id="avatar" />
                        @else
                        <div
                            class="flex items-center justify-center bg-zinc-100 hover:bg-zinc-200 duration-200 ease-in-out rounded-full border dark:bg-zinc-700 dark:hover:bg-zinc-800 size-24 mt-2">
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
                    <div class="items-center space-y-2">
                        <p>{{ Str::afterLast($avatar, '/') }}</p>

                        <flux:button variant="outline" type="button" wire:click="removeAvatar" size="sm">
                            Remove
                        </flux:button>
                    </div>
                    @endif
                </div>
            </div>

            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus
                autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />
            </div>

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