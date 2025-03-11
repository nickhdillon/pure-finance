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
    livewire(CategoryForm::class, [
        'parent_categories' => auth()
            ->user()
            ->categories()
            ->with('parent')
            ->select(['id', 'name', 'parent_id'])
            ->whereNull('parent_id')
            ->get()
    ])
        ->set('name', 'Test category')
        ->call('submit')
        ->assertDispatched('category-saved')
        ->assertHasNoErrors();
});

it('can edit a category', function () {
    livewire(CategoryForm::class, [
        'parent_categories' => auth()
            ->user()
            ->categories()
            ->with('parent')
            ->select(['id', 'name', 'parent_id'])
            ->whereNull('parent_id')
            ->get()
    ])
        ->call('loadCategory', auth()->user()->categories->first()->toArray())
        ->set('name', 'Test category updated')
        ->call('submit')
        ->assertDispatched('category-saved')
        ->assertHasNoErrors();
});

it('can reset the form', function () {
    livewire(CategoryForm::class, [
        'parent_categories' => auth()
            ->user()
            ->categories()
            ->with('parent')
            ->select(['id', 'name', 'parent_id'])
            ->whereNull('parent_id')
            ->get()
    ])
        ->call('loadCategory', auth()->user()->categories->first()->toArray())
        ->set('name', 'Test category')
        ->call('resetForm')
        ->assertSet('category', null)
        ->assertSet('name', '')
        ->assertSet('parent_id', null)
        ->assertHasNoErrors();
});

test('component can render', function () {
    livewire(CategoryForm::class, [
        'parent_categories' => auth()
            ->user()
            ->categories()
            ->with('parent')
            ->select(['id', 'name', 'parent_id'])
            ->whereNull('parent_id')
            ->get()
    ])
        ->assertHasNoErrors();
});
