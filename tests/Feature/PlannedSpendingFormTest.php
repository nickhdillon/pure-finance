<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Category;
use App\Models\PlannedExpense;
use function Pest\Livewire\livewire;
use App\Livewire\PlannedSpendingForm;

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

it('can create an expense', function () {
    livewire(PlannedSpendingForm::class)
        ->set('name', 'Test expense')
        ->set('category_id', Category::first()->id)
        ->set('monthly_amount', 80)
        ->call('submit')
        ->assertDispatched('planned-expense-saved')
        ->assertHasNoErrors();
});

it('can edit an expense', function () {
    livewire(PlannedSpendingForm::class, ['expense' => PlannedExpense::factory()->create()])
        ->set('name', 'Test expense updated')
        ->call('submit')
        ->assertDispatched('planned-expense-saved')
        ->assertHasNoErrors();
});

it('can delete an expense', function () {
    livewire(PlannedSpendingForm::class, ['expense' => PlannedExpense::factory()->create()])
        ->call('delete')
        ->assertHasNoErrors();

    $this->assertDatabaseCount('planned_expenses', 0);
});

test('component can render', function () {
    livewire(PlannedSpendingForm::class)
        ->assertHasNoErrors();
});
