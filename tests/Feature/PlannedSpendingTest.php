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

it('can sort expenses by amount desc', function () {
    PlannedExpense::factory()->create(['name' => 'Low', 'monthly_amount' => 50]);
    PlannedExpense::factory()->create(['name' => 'High', 'monthly_amount' => 200]);
    PlannedExpense::factory()->create(['name' => 'Mid', 'monthly_amount' => 100]);

    livewire(PlannedSpending::class)
        ->call('sortBy', 'planned_amount', 'desc')
        ->assertHasNoErrors()
        ->assertSeeInOrder(['High', 'Mid', 'Low']);
});

it('can sort expenses by amount asc', function () {
    PlannedExpense::factory()->create(['name' => 'Low', 'monthly_amount' => 50]);
    PlannedExpense::factory()->create(['name' => 'High', 'monthly_amount' => 200]);
    PlannedExpense::factory()->create(['name' => 'Mid', 'monthly_amount' => 100]);

    livewire(PlannedSpending::class)
        ->call('sortBy', 'planned_amount', 'asc')
        ->assertHasNoErrors()
        ->assertSeeInOrder(['Low', 'Mid', 'High']);
});

it('can sort expenses by name A-Z', function () {
    PlannedExpense::factory()->create(['name' => 'Groceries']);
    PlannedExpense::factory()->create(['name' => 'Car']);
    PlannedExpense::factory()->create(['name' => 'Utilities']);

    livewire(PlannedSpending::class)
        ->call('sortBy', 'name', 'asc')
        ->assertHasNoErrors()
        ->assertSeeInOrder(['Car', 'Groceries', 'Utilities']);
});

it('can sort expenses by name Z-A', function () {
    PlannedExpense::factory()->create(['name' => 'Groceries']);
    PlannedExpense::factory()->create(['name' => 'Car']);
    PlannedExpense::factory()->create(['name' => 'Utilities']);

    livewire(PlannedSpending::class)
        ->call('sortBy', 'name', 'desc')
        ->assertHasNoErrors()
        ->assertSeeInOrder(['Utilities', 'Groceries', 'Car']);
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
