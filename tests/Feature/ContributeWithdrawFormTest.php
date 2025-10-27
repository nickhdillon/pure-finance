<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Account;
use App\Models\SavingsGoal;
use App\Livewire\ContributeWithdrawForm;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());

    Account::factory()
        ->for(User::first())
        ->has(
            SavingsGoal::factory(),
            'savings_goals'
        )
        ->create();
});

it('can contribute to goal', function () {
    livewire(ContributeWithdrawForm::class, ['savings_goal' => SavingsGoal::first()])
        ->set('contribution_amount', 200)
        ->set('deduct_from_account', true)
        ->call('submit')
        ->assertDispatched('savings-goal-saved')
        ->assertHasNoErrors();
});

it('can withdraw from goal', function () {
    livewire(ContributeWithdrawForm::class, ['savings_goal' => SavingsGoal::first()])
        ->set('withdrawal_amount', 100)
        ->set('add_to_account', true)
        ->call('submit')
        ->assertDispatched('savings-goal-saved')
        ->assertHasNoErrors();
});

test('component can render', function () {
    livewire(ContributeWithdrawForm::class, ['savings_goal' => SavingsGoal::first()])
        ->assertHasNoErrors();
});
