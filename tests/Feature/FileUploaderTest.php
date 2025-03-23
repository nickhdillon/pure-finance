<?php

declare(strict_types=1);

use App\Livewire\FileUploader;
use Illuminate\Http\UploadedFile;
use function Pest\Livewire\livewire;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');
});

it('can upload a file', function () {
    $file = UploadedFile::fake()->image('files/logo.png');

    livewire(FileUploader::class)
        ->set('files', $file)
        ->assertDispatched('file-uploaded')
        ->assertHasNoErrors();

    Storage::disk('s3')->assertExists('files/logo.png');
});

it('can format file size', function () {
    $file = UploadedFile::fake()->image('files/logo.png');

    livewire(FileUploader::class)
        ->call('formatFileSize', $file->getSize())
        ->assertHasNoErrors();
});

it('can upload and remove a file', function () {
    $file = UploadedFile::fake()->image('files/logo.png');

    livewire(FileUploader::class)
        ->set('files', $file)
        ->call('removeFile', 'logo.png')
        ->assertDispatched('file-deleted')
        ->assertHasNoErrors();
});

it('can pass in a file', function () {
    $file = UploadedFile::fake()->image('files/logo.png');

    livewire(FileUploader::class, [
        'files' => [
            [
                'name' => 'logo.png',
                'size' => $file->getSize(),
            ]
        ]
    ])
        ->assertSet('uploaded_files', collect([
            [
                'name' => 'logo.png',
                'size' => $file->getSize(),
            ]
        ]))
        ->assertHasNoErrors();
});

test('component can render', function () {
    livewire(FileUploader::class)
        ->assertHasNoErrors();
});
