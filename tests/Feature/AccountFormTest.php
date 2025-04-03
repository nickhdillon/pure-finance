<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Account;
use App\Enums\AccountType;
use App\Livewire\AccountForm;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->actingAs(
        User::factory()
            ->hasAccounts(Account::factory())
            ->create()
    );
});

it('can create an account', function () {
    livewire(AccountForm::class)
        ->set('name', 'Checking Account')
        ->set('type', AccountType::CHECKING)
        ->call('submit')
        ->assertHasNoErrors();

    $this->assertDatabaseCount('accounts', 2);
});

it('can update an account', function () {
    livewire(AccountForm::class, ['account' => Account::first()])
        ->set('name', 'Updated name')
        ->call('submit')
        ->assertHasNoErrors();

    $this->assertDatabaseCount('accounts', 1);
});

it('can delete an account', function () {
    livewire(AccountForm::class, ['account' => Account::first()])
        ->call('delete')
        ->assertHasNoErrors();

    $this->assertDatabaseCount('accounts', 0);
});

test('component can render', function () {
    livewire(AccountForm::class)
        ->assertHasNoErrors();
});
