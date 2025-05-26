<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Account;
use App\Models\SavingsGoal;
use App\Livewire\SavingsGoalView;
use function Pest\Livewire\livewire;
use App\Models\SavingsGoalTransaction;

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

test('component can render with history', function () {
    $savings_goal = SavingsGoal::first();

    SavingsGoalTransaction::factory()
        ->for($savings_goal, 'savings_goal')
        ->create([
            'contribution_amount' => 200,
            'withdrawal_amount' => 0,
        ]);

    livewire(SavingsGoalView::class, ['savings_goal' => $savings_goal])
        ->assertSee($savings_goal->name)
        ->assertSee('History')
        ->assertHasNoErrors();
});

test('component can render with no history', function () {
    $savings_goal = SavingsGoal::first();

    livewire(SavingsGoalView::class, ['savings_goal' => $savings_goal])
        ->assertSee($savings_goal->name)
        ->assertDontSee('History')
        ->assertHasNoErrors();
});
