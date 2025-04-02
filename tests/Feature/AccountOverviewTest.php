<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Account;
use App\Livewire\AccountOverview;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->actingAs(
        User::factory()
            ->hasAccounts(Account::factory())
            ->create()
    );
});

test('can see account form', function () {
    livewire(AccountOverview::class, ['account' => Account::first()])
        ->assertSeeLivewire('account-form')
        ->assertHasNoErrors();
});

test('can see transactions table', function () {
    livewire(AccountOverview::class, ['account' => Account::first()])
        ->assertSeeLivewire('transaction-table')
        ->assertHasNoErrors();
});

test('component can render', function () {
    livewire(AccountOverview::class, ['account' => Account::first()])
        ->assertHasNoErrors();
});
