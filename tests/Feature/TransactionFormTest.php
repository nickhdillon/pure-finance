<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Account;
use App\Models\Category;
use App\Enums\AccountType;
use App\Models\Transaction;
use App\Enums\TransactionType;
use App\Enums\RecurringFrequency;
use App\Livewire\TransactionForm;
use Illuminate\Http\UploadedFile;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->withoutDefer();

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
        ->has(Transaction::factory()->count(10))
        ->create();

    $this->actingAs($user);
});

it('can create a transaction', function () {
    livewire(TransactionForm::class)
        ->set('account_id', auth()->user()->accounts->first()->id)
        ->set('payee', 'Test payee')
        ->set('type', TransactionType::DEPOSIT)
        ->set('amount', 100)
        ->set('category_id', auth()->user()->categories->first()->id)
        ->set('date', now())
        ->set('notes', '')
        ->set('status', true)
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect();
});

it('can edit a transaction', function () {
    livewire(TransactionForm::class, [
        'transaction' => auth()->user()->transactions->first(),
    ])
        ->set('type', TransactionType::WITHDRAWAL)
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect();
});

it('can make transaction recurring by month', function () {
    $transaction = Transaction::factory()
        ->for(auth()->user()->accounts()->first())
        ->create([
            'type' => TransactionType::DEBIT,
        ]);

    livewire(TransactionForm::class, [
        'transaction' => $transaction,
    ])
        ->set('date', now())
        ->set('is_recurring', true)
        ->set('frequency', RecurringFrequency::MONTHLY)
        ->set('recurring_end', now()->addMonth())
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect();
});

it('can make transaction recurring by year', function () {
    $transaction = Transaction::factory()
        ->for(auth()->user()->accounts()->first())
        ->create([
            'type' => TransactionType::CREDIT,
        ]);

    livewire(TransactionForm::class, [
        'transaction' => $transaction,
    ])
        ->set('date', now())
        ->set('is_recurring', true)
        ->set('frequency', RecurringFrequency::YEARLY)
        ->set('recurring_end', now()->addYear())
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect();
});

it('can see validation error if end date is before start date', function () {
    livewire(TransactionForm::class, [
        'transaction' => auth()->user()->transactions->first(),
    ])
        ->set('date', now())
        ->set('is_recurring', true)
        ->set('frequency', RecurringFrequency::MONTHLY)
        ->set('recurring_end', now()->subWeek())
        ->call('submit')
        ->assertSee('The end date must be after the start date.')
        ->assertHasErrors()
        ->assertNoRedirect();
});

it('can push to attachments', function () {
    $file = UploadedFile::fake()->image('pure-finance/files/logo.png');

    livewire(TransactionForm::class)
        ->call('pushToAttachments', [
            'name' => 'logo.png',
            'size' => $file->getSize(),
        ])
        ->assertHasNoErrors();
});

it('can delete an attachment', function () {
    UploadedFile::fake()->image('pure-finance/files/logo.png');

    livewire(TransactionForm::class, [
        'transaction' => auth()->user()->transactions->first(),
    ])
        ->call('deleteAttachment', 'logo.png')
        ->assertHasNoErrors();
});

it('can delete a transaction', function () {
    livewire(TransactionForm::class, [
        'account' => auth()->user()->accounts()->first()
    ])
        ->call('delete', Transaction::first()->id)
        ->assertHasNoErrors()
        ->assertRedirect();
});

it('can transfer from one account to another', function () {
    auth()->user()->accounts()->create([
        'type' => AccountType::CHECKING,
        'name' => 'Checking',
    ]);

    livewire(TransactionForm::class)
        ->set('account_id', auth()->user()->accounts->first()->id)
        ->set('payee', 'Test payee')
        ->set('type', TransactionType::TRANSFER)
        ->set('transfer_to', auth()->user()->accounts->last()->id)
        ->set('amount', 100)
        ->set('category_id', auth()->user()->categories->first()->id)
        ->set('date', now())
        ->set('notes', '')
        ->set('status', true)
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect();
});

test('component can render with transaction', function () {
    livewire(TransactionForm::class, [
        'transaction' => auth()->user()->transactions->first(),
    ])
        ->assertHasNoErrors();
});

test('component can render with account and transaction', function () {
    livewire(TransactionForm::class, [
        'account' => auth()->user()->accounts->first(),
        'transaction' => auth()->user()->transactions->first(),
    ])
        ->assertHasNoErrors();
});

test('component can render', function () {
    livewire(TransactionForm::class)
        ->assertHasNoErrors();
});
