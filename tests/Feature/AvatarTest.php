<?php

declare(strict_types=1);

use App\Models\User;
use App\Livewire\Avatar;
use Illuminate\Http\UploadedFile;
use function Pest\Livewire\livewire;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');

    $this->actingAs(User::factory()->create());
});

it('can save avatar', function () {
    livewire(Avatar::class)
        ->set('avatar', UploadedFile::fake()->image('test1.jpg'))
        ->call('save', [
            'width' => 500,
            'height' => 500,
            'x' => 0,
            'y' => 0
        ])
        ->assertHasNoErrors()
        ->assertRedirect();
});

it('can clear avatar', function () {
    livewire(Avatar::class)
        ->call('clearAvatar')
        ->assertSet('avatar', null)
        ->assertHasNoErrors()
        ->assertRedirect();
});

it('can remove avatar', function () {
    $file = UploadedFile::fake()->image('test1.jpg');

    $file_path = Storage::disk('s3')->putFile('avatars', $file);

    auth()->user()->update(['avatar' => basename($file_path)]);

    livewire(Avatar::class)
        ->set('avatar', $file)
        ->call('removeAvatar')
        ->assertHasNoErrors()
        ->assertRedirect();

    $this->assertFalse(Storage::disk('s3')->exists("avatars/{$file->hashName()}"));

    $this->assertNull(auth()->user()->avatar);
});

test('component can render', function () {
    livewire(Avatar::class)
        ->assertHasNoErrors();
});
