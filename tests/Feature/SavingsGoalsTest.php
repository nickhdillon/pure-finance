<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Account;
use App\Models\SavingsGoal;
use App\Livewire\SavingsGoals;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('component can render with savings goals', function () {
    Account::factory()
        ->for(User::first())
        ->has(
            SavingsGoal::factory(),
            'savings_goals'
        )
        ->create();

    livewire(SavingsGoals::class)
        ->assertHasNoErrors();

    $this->assertDatabaseCount('savings_goals', 1);
});

test('component can render with no savings goals', function () {
    livewire(SavingsGoals::class)
        ->assertSee('No savings goals found')
        ->assertHasNoErrors();
});
