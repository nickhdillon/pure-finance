<?php

declare(strict_types=1);

use App\Enums\RecurringFrequency;
use App\Enums\TransactionType;
use App\Models\Bill;
use App\Models\User;
use App\Models\Account;
use App\Models\Category;
use App\Livewire\BillForm;
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

    Account::factory()
        ->for($user)
        ->create();

    Bill::factory(10)
        ->for($user)
        ->create(['frequency' => RecurringFrequency::MONTHLY]);

    $this->actingAs($user);
});

it('can create a bill', function () {
    $user = User::first();

    livewire(BillForm::class)
        ->set('account_id', $user->accounts()->first()->id)
        ->set('name', 'Test Bill')
        ->set('type', TransactionType::DEBIT)
        ->set('category_id', $user->categories()->first()->id)
        ->set('amount', 100)
        ->set('date', now()->toDateString())
        ->set('frequency', RecurringFrequency::MONTHLY)
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirectToRoute('bill-calendar');
});

it('can edit a bill', function () {
    livewire(BillForm::class)
        ->call('loadBill', auth()->user()->bills->first()->id)
        ->set('name', 'Test Bill Updated')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirectToRoute('bill-calendar');
});

it('can edit a bill and update all children', function () {
    livewire(BillForm::class)
        ->call('loadBill', auth()->user()->bills->first()->id)
        ->set('name', 'Test Bill Updated')
        ->call('submit', all: true)
        ->assertHasNoErrors()
        ->assertRedirectToRoute('bill-calendar');
});

it('can reset the form', function () {
    livewire(BillForm::class)
        ->call('loadBill', auth()->user()->bills->first()->id)
        ->set('name', 'Test bill')
        ->call('resetForm')
        ->assertSet('bill', null)
        ->assertSet('name', '')
        ->assertHasNoErrors();
});

test('component can render', function () {
    livewire(BillForm::class)
        ->assertHasNoErrors();
});
