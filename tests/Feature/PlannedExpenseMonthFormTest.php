<?php

declare(strict_types=1);

use App\Livewire\PlannedExpenseMonthForm;
use App\Models\Category;
use App\Models\PlannedExpense;
use App\Models\PlannedExpenseMonth;
use App\Models\User;

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

it('can edit an expense', function () {
    livewire(PlannedExpenseMonthForm::class, ['expense_month' => PlannedExpenseMonth::factory()->create()])
        ->set('amount', 80)
        ->call('submit')
        ->assertDispatched('planned-expense-saved')
        ->assertHasNoErrors();
});

it('can edit an expense and apply to future months', function () {
    livewire(PlannedExpenseMonthForm::class, ['expense_month' => PlannedExpenseMonth::factory()->create()])
        ->set('amount', 200)
        ->set('apply_to_future_months', true)
        ->call('submit')
        ->assertDispatched('planned-expense-saved')
        ->assertHasNoErrors();
});

it('can delete an expense', function () {
    $expense = PlannedExpense::factory()->create();
    $expense_months = PlannedExpenseMonth::factory()
        ->count(2)
        ->forPlannedExpense($expense)
        ->sequence(
            ['month' => now()->startOfMonth()],
            ['month' => now()->addMonth()->startOfMonth()],
        )
        ->create();

    livewire(PlannedExpenseMonthForm::class, ['expense_month' => $expense_months->first()])
        ->call('delete')
        ->assertRedirectToRoute('planned-spending')
        ->assertHasNoErrors();

    expect($expense->fresh())->toBeNull()
        ->and(PlannedExpenseMonth::query()
            ->where('planned_expense_id', $expense->id)
            ->exists())->toBeFalse();
});

test('component can render', function () {
    livewire(PlannedExpenseMonthForm::class, ['expense_month' => PlannedExpenseMonth::factory()->create()])
        ->assertHasNoErrors();
});
