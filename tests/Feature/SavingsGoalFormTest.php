<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Account;
use App\Models\SavingsGoal;
use App\Livewire\SavingsGoalForm;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->actingAs(
        User::factory()
            ->has(Account::factory())
            ->create()
    );
});

it('can create a savings goal', function () {
    livewire(SavingsGoalForm::class)
        ->set('account_id', Account::first()->id)
        ->set('name', 'Test goal')
        ->set('goal_amount', 1000)
        ->set('monthly_contribution', 500)
        ->call('submit')
        ->assertDispatched('savings-goal-saved')
        ->assertHasNoErrors();
});

it('can edit a savings goal', function () {
    livewire(SavingsGoalForm::class, ['savings_goal' => SavingsGoal::factory()->create()])
        ->set('name', 'Test goal updated')
        ->call('submit')
        ->assertDispatched('savings-goal-saved')
        ->assertHasNoErrors();
});

it('can delete a savings goal', function () {
    livewire(SavingsGoalForm::class, ['savings_goal' => SavingsGoal::factory()->create()])
        ->call('delete')
        ->assertHasNoErrors();

    $this->assertDatabaseCount('savings_goals', 0);
});

test('component can render', function () {
    livewire(SavingsGoalForm::class)
        ->assertHasNoErrors();
});
