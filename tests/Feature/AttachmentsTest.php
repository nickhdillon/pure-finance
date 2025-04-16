<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Livewire\Attachments;
use Illuminate\Http\UploadedFile;
use function Pest\Livewire\livewire;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');

    $user = User::factory()->create();

    if (Category::count() === 0) {
        $categories = collect([
            'Personal Income',
            'Pets',
            'Shopping',
            'Travel',
            'Utilities',
        ]);

        $categories->each(function (string $name) use ($user): void {
            Category::factory()->for($user)->create([
                'name' => $name
            ]);
        });
    }

    Account::factory()
        ->for($user)
        ->has(Transaction::factory()->state([
            'attachments' => [
                [
                    'name' => Storage::disk('s3')->putFile(
                        'attachments',
                        UploadedFile::fake()->image('test1.jpg')
                    ),
                    'original_name' => 'test.jpg'
                ],
                [
                    'name' => Storage::disk('s3')->putFile(
                        'attachments',
                        UploadedFile::fake()->image('test2.jpg')
                    ),
                    'original_name' => 'test.jpg'
                ]
            ]
        ])->count(10))
        ->create();

    $this->actingAs($user);
});

it('can load attachments', function () {
    $attachments = auth()->user()->transactions->first()->attachments;

    livewire(Attachments::class)
        ->call('loadAttachments', $attachments)
        ->assertSet('attachments', $attachments)
        ->assertHasNoErrors();
});

it('can reset attachments', function () {
    livewire(Attachments::class)
        ->call('resetAttachments')
        ->assertSet('attachments', [])
        ->assertHasNoErrors();
});

test('component can render', function () {
    livewire(Attachments::class)
        ->assertHasNoErrors();
});
