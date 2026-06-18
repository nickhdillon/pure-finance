<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Category;
use App\Models\PlannedExpenseMonth;
use function Pest\Livewire\livewire;
use App\Livewire\PlannedExpenseMonthForm;

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
    livewire(PlannedExpenseMonthForm::class, ['expense_month' => PlannedExpenseMonth::factory()->create()])
        ->call('delete')
        ->assertRedirectToRoute('planned-spending')
        ->assertHasNoErrors();
});

test('component can render', function () {
    livewire(PlannedExpenseMonthForm::class, ['expense_month' => PlannedExpenseMonth::factory()->create()])
        ->assertHasNoErrors();
});
