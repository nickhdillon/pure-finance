<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Category;
use App\Models\PlannedExpense;
use App\Livewire\PlannedSpending;
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

test('component can render with planned expenses', function () {
    PlannedExpense::factory()->count(5)->create();

    livewire(PlannedSpending::class)
        ->assertSee('Personal Income')
        ->assertHasNoErrors();
});

test('component can render with no planned expenses', function () {
    livewire(PlannedSpending::class)
        ->assertSee('No expenses found')
        ->assertHasNoErrors();
});
