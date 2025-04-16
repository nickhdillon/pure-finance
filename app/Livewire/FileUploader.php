<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class FileUploader extends Component
{
    use WithFileUploads;

    public string $input_uuid = '';

    #[Validate([
        'files' => ['required', 'array'],
        'files.*' => ['file', 'max:12288', 'mimes:jpg,jpeg,png,heic,svg,avif,webp'],
    ])]
    public ?array $files = [];

    public Collection $uploaded_files;

    public string $selected_file = '';

    public string $s3_path = 'files';

    public bool $disabled = false;

    protected function messages(): array
    {
        return [
            'files.*.file' => 'File must be a valid file',
            'files.*.max' => 'File must be less than 12MB',
            'files.*.mimes' => 'File must be of type: jpg, jpeg, png, heic, svg, avif, webp',
        ];
    }

    public function mount(): void
    {
        $this->uploaded_files = collect();

        if ($this->files) {
            foreach ($this->files as $file) {
                $this->uploaded_files->push($file);
            }
        }
    }

    private function resetFileInput(): void
    {
        $this->input_uuid = (string) Str::uuid();
        $this->reset('files');
    }

    public function formatFileSize(int $bytes): string
    {
        return match (true) {
            $bytes >= 1073741824 => round($bytes / 1073741824, 2) . ' GB',
            $bytes >= 1048576 => round($bytes / 1048576, 2) . ' MB',
            $bytes >= 1024 => round($bytes / 1024, 2) . ' KB',
            default => $bytes . ' Bytes',
        };
    }

    public function updatedFiles(): void
    {
        $this->validate();

        foreach ($this->files as $file) {
            $uuid = Str::uuid()->toString();

            $converted_image = Image::read($file)->encodeByExtension('jpg');

            $file_name = Str::beforeLast($file->getClientOriginalName(), '.') . '.jpg';

            $unique_file_name = now()->timestamp . '_' . $file_name;

            $this->uploaded_files->push([
                'id' => $uuid,
                'name' => $unique_file_name,
                'original_name' => $file_name,
                'size' => $this->formatFileSize($converted_image->size()),
            ]);

            Storage::disk('s3')->put("{$this->s3_path}/{$unique_file_name}", $converted_image, 'public');

            $this->dispatch('file-uploaded', file: [
                'id' => $uuid,
                'name' => $unique_file_name,
                'original_name' => $file_name,
                'size' => $this->formatFileSize($file->getSize()),
            ]);
        }

        $this->resetFileInput();
    }

    public function removeFile(string $file_name, string $file_id): void
    {
        if (Storage::disk('s3')->exists("{$this->s3_path}/{$file_name}")) {
            Storage::disk('s3')->delete("{$this->s3_path}/{$file_name}");
        }

        $this->uploaded_files = $this->uploaded_files
            ->reject(fn(array $file): bool => $file['id'] === $file_id)
            ->values();

        $this->dispatch('file-deleted', $file_id);

        $this->resetFileInput();
    }

    #[On('file-deleted')]
    public function render(): View
    {
        return view('livewire.file-uploader');
    }
}
