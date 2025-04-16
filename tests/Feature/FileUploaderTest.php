<?php

declare(strict_types=1);

use Illuminate\Support\Str;
use App\Livewire\FileUploader;
use Illuminate\Http\UploadedFile;
use function Pest\Livewire\livewire;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');
});

it('can upload a file', function () {
    $file = UploadedFile::fake()->image('files/logo.jpg');

    livewire(FileUploader::class)
        ->set('files', $file)
        ->assertDispatched('file-uploaded')
        ->assertHasNoErrors();

    $file_name = now()->timestamp . '_logo.jpg';

    Storage::disk('s3')->assertExists("files/{$file_name}");
});

it('can format file size', function () {
    $file = UploadedFile::fake()->image('files/logo.jpg');

    livewire(FileUploader::class)
        ->call('formatFileSize', $file->getSize())
        ->assertHasNoErrors();
});

it('can upload and remove a file', function () {
    $file = UploadedFile::fake()->image('files/logo.jpg');

    $uuid = Str::uuid()->toString();

    $file_name = now()->timestamp . '_logo.jpg';

    livewire(FileUploader::class)
        ->set('files', $file)
        ->call('removeFile', $file_name, $uuid)
        ->assertDispatched('file-deleted')
        ->assertHasNoErrors();
});

it('can pass in a file', function () {
    $file = UploadedFile::fake()->image('files/logo.jpg');

    $uuid = Str::uuid()->toString();

    livewire(FileUploader::class, [
        'files' => [
            [
                'id' => $uuid,
                'name' => 'logo.jpg',
                'original_name' => 'logo.jpg',
                'size' => $file->getSize(),
            ],
        ],
    ])
        ->assertSet('uploaded_files', collect([
            [
                'id' => $uuid,
                'name' => 'logo.jpg',
                'original_name' => 'logo.jpg',
                'size' => $file->getSize(),
            ],
        ]))
        ->assertHasNoErrors();
});

test('component can render', function () {
    livewire(FileUploader::class)
        ->assertHasNoErrors();
});
