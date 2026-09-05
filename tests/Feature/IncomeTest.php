<?php

declare(strict_types=1);

use App\Enums\IncomeType;
use App\Enums\TransactionType;
use App\Livewire\Income;
use App\Livewire\IncomeForm;
use App\Models\Account;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

it('can create expected and unplanned income', function (): void {
    livewire(IncomeForm::class)
        ->set('name', 'Paycheck')
        ->set('amount', '2500.00')
        ->set('type', IncomeType::EXPECTED)
        ->set('date', '2026-09-15')
        ->call('submit')
        ->assertHasNoErrors();

    livewire(IncomeForm::class)
        ->set('name', 'Birthday gift')
        ->set('amount', '100.00')
        ->set('type', IncomeType::UNPLANNED)
        ->set('date', '2026-09-04')
        ->call('submit')
        ->assertHasNoErrors();

    expect(auth()->user()->incomes()->where('type', IncomeType::EXPECTED)->sum('amount'))->toBe(2500)
        ->and(auth()->user()->incomes()->where('type', IncomeType::UNPLANNED)->sum('amount'))->toBe(100);
});

it('resets the form after creating income so another entry is created', function (): void {
    livewire(IncomeForm::class)
        ->set('name', 'First paycheck')
        ->set('amount', '2500.00')
        ->set('type', IncomeType::EXPECTED)
        ->set('date', '2026-09-15')
        ->call('submit')
        ->assertSet('income', null)
        ->set('name', 'Second paycheck')
        ->set('amount', '2500.00')
        ->set('type', IncomeType::EXPECTED)
        ->set('date', '2026-09-30')
        ->call('submit')
        ->assertHasNoErrors();

    expect(auth()->user()->incomes()->count())->toBe(2);
});

it('resets an edited income when the form closes', function (): void {
    $income = auth()->user()->incomes()->create([
        'name' => 'Existing paycheck',
        'amount' => 2500,
        'type' => IncomeType::EXPECTED,
        'date' => '2026-09-15',
    ]);

    livewire(IncomeForm::class)
        ->call('loadIncome', $income->id)
        ->call('resetForm')
        ->assertSet('income', null)
        ->assertSet('name', '')
        ->assertSet('amount', '');
});

it('only allows a user to edit their own income', function (): void {
    $otherUser = User::factory()->create();
    $income = $otherUser->incomes()->create([
        'name' => 'Other paycheck',
        'amount' => 1000,
        'type' => IncomeType::EXPECTED,
        'date' => '2026-09-15',
    ]);

    livewire(IncomeForm::class)->call('loadIncome', $income->id);
})->throws(ModelNotFoundException::class);

it('can create income that was already received without creating a transaction', function (): void {
    livewire(IncomeForm::class)
        ->set('name', 'Paycheck')
        ->set('amount', '2500.00')
        ->set('type', IncomeType::EXPECTED)
        ->set('date', '2026-09-15')
        ->set('received', true)
        ->call('submit')
        ->assertHasNoErrors();

    $income = auth()->user()->incomes()->sole();

    expect($income->received)->toBeTrue()
        ->and($income->transaction_id)->toBeNull();
});

it('creates a deposit transaction when income is marked received', function (): void {
    $account = Account::factory()->for(auth()->user())->create();
    $category = Category::factory()->for(auth()->user())->create();
    $income = auth()->user()->incomes()->create([
        'name' => 'Paycheck',
        'amount' => 2500,
        'type' => IncomeType::EXPECTED,
        'date' => '2026-09-15',
    ]);

    livewire(Income::class)
        ->call('confirmReceipt', $income->id)
        ->set('receiving_account_id', $account->id)
        ->set('category_id', $category->id)
        ->call('markReceived')
        ->assertHasNoErrors();

    $income->refresh();

    expect($income->received)->toBeTrue()
        ->and($income->transaction)->not->toBeNull()
        ->and($income->transaction->account_id)->toBe($account->id)
        ->and($income->transaction->category_id)->toBe($category->id)
        ->and($income->transaction->type)->toBe(TransactionType::DEPOSIT)
        ->and($income->transaction->amount)->toBe(2500.0);
});
