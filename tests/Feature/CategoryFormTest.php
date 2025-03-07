<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Category;
use App\Livewire\CategoryForm;
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
                'name' => $name
            ]);
        });
    }

    $this->actingAs($user);
});

it('can create a category', function () {
    livewire(CategoryForm::class)
        ->set('name', 'Test category')
        ->call('submit')
        ->assertDispatched('category-saved')
        ->assertHasNoErrors();
});

it('can edit a category', function () {
    livewire(CategoryForm::class, ['category' => auth()->user()->categories->first()])
        ->set('name', 'Test category updated')
        ->call('submit')
        ->assertDispatched('category-saved')
        ->assertHasNoErrors();
});

test('component can render', function () {
    livewire(CategoryForm::class)
        ->assertHasNoErrors();
});
