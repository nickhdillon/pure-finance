<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use Spatie\Image\Image;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class Avatar extends Component
{
    use WithFileUploads;

    public TemporaryUploadedFile|string|null $avatar = null;

    public string $s3_path = 'avatars';

    public bool $show_crop_avatar_modal = false;

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
            'avatar' => ['nullable', 'image', 'max:190000', 'mimes:jpg,jpeg,png']
        ];
    }

    public function mount(): void
    {
        $this->avatar = auth()->user()->avatar;
    }

    #[Computed]
    public function avatarUrl(): string
    {
        if ($this->avatar instanceof TemporaryUploadedFile) {
            return $this->avatar->temporaryUrl();
        }

        if (is_string($this->avatar) && Storage::disk('s3')->exists("{$this->s3_path}/{$this->avatar}")) {
            return Storage::disk('s3')->url("{$this->s3_path}/{$this->avatar}");
        }

        return '';
    }

    public function updatedAvatar(): void
    {
        $this->validate();

        $this->show_crop_avatar_modal = true;
    }

    public function save(array $crop_region): void
    {
        $crop_region = array_map('intval', $crop_region);

        Image::load($this->avatar->getRealPath())
            ->manualCrop(
                $crop_region['width'],
                $crop_region['height'],
                $crop_region['x'],
                $crop_region['y'],
            )
            ->save();

        /** @var TemporaryUploadedFile $avatar */
        $avatar = $this->avatar;

        $filename = $avatar->getClientOriginalName();

        $avatar->storePubliclyAs($this->s3_path, $filename, 's3');

        auth()
            ->user()
            ->update(['avatar' => $filename]);

        $this->redirectRoute('settings.profile', navigate: true);
    }

    public function clearAvatar(): void
    {
        $this->reset('avatar');

        $this->redirectRoute('settings.profile', navigate: true);
    }

    public function removeAvatar(): void
    {
        $user = auth()->user();

        if ($user->avatar && Storage::disk('s3')->exists("{$this->s3_path}/{$user->avatar}")) {
            Storage::disk('s3')->delete("{$this->s3_path}/{$user->avatar}");
        }

        $user->update(['avatar' => null]);

        $this->redirectRoute('settings.profile', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.avatar');
    }
}
