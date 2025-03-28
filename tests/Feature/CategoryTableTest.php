<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Category;
use App\Livewire\CategoryTable;
use function Pest\Livewire\livewire;

beforeEach(function () {
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
                'name' => $name,
            ]);
        });
    }

    $this->actingAs($user);
});

it('can search a category', function () {
    livewire(CategoryTable::class)
        ->set('search', 'Pets')
        ->assertHasNoErrors();
});

it('can delete a category', function () {
    livewire(CategoryTable::class)
        ->call('delete', auth()->user()->categories->first()->id)
        ->assertHasNoErrors();
});

test('component can render', function () {
    livewire(CategoryTable::class)
        ->assertHasNoErrors();
});
