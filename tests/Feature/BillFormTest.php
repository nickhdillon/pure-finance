<?php

declare(strict_types=1);

use App\Models\Bill;
use App\Models\User;
use App\Models\Account;
use App\Models\Category;
use App\Livewire\BillForm;
use App\Enums\TransactionType;
use App\Enums\RecurringFrequency;
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
        ->create();

    Bill::factory(10)
        ->for($user)
        ->create(['paid' => false]);

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

it('can see validation error when only second alert is set', function () {
    livewire(BillForm::class)
        ->call('loadBill', auth()->user()->bills->first()->id)
        ->set('name', 'Test Bill Updated')
        ->set('first_alert', null)
        ->set('first_alert_time', null)
        ->call('submit')
        ->assertHasErrors()
        ->assertNoRedirect();
});

it('can create a bill and update all children', function () {
    $user = User::first();

    livewire(BillForm::class)
        ->set('account_id', $user->accounts()->first()->id)
        ->set('name', 'New Test Bill')
        ->set('type', TransactionType::DEBIT)
        ->set('category_id', $user->categories()->first()->id)
        ->set('amount', 100)
        ->set('date', now()->toDateString())
        ->set('frequency', RecurringFrequency::MONTHLY)
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirectToRoute('bill-calendar');

    $bill = Bill::where('name', 'New Test Bill')->first();

    livewire(BillForm::class)
        ->call('loadBill', $bill->id)
        ->set('name', 'New Test Bill Updated')
        ->call('submit', all: true)
        ->assertHasNoErrors()
        ->assertRedirectToRoute('bill-calendar');
});

it('can set the category on event', function () {
    $category = auth()->user()->categories()->create([
        'name' => 'Test'
    ]);

    livewire(BillForm::class)
        ->call('setCategory')
        ->assertSet('category_id', $category->id)
        ->assertHasNoErrors();
});

it('can push to attachments', function () {
    $file = UploadedFile::fake()->image('pure-finance/files/logo.png');

    livewire(BillForm::class)
        ->call('pushToAttachments', [
            'name' => 'logo.png',
            'size' => $file->getSize(),
        ])
        ->assertHasNoErrors();
});

it('can delete an attachment', function () {
    UploadedFile::fake()->image('pure-finance/files/logo.png');

    livewire(BillForm::class)
        ->call('loadBill', auth()->user()->bills->first()->id)
        ->call('deleteAttachment', 'logo.png')
        ->assertHasNoErrors();
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

it('can mark a bill as paid', function () {
    livewire(BillForm::class)
        ->call('loadBill', auth()->user()->bills->first()->id)
        ->call('changePaidStatus', create_related_transaction: true)
        ->assertHasNoErrors()
        ->assertRedirectToRoute('bill-calendar');
});

it('can mark a bill as unpaid', function () {
    livewire(BillForm::class)
        ->call('loadBill', auth()->user()->bills->first()->id)
        ->set('paid', true)
        ->call('changePaidStatus')
        ->assertHasNoErrors()
        ->assertRedirectToRoute('bill-calendar');
});

it('can delete a bill', function () {
    livewire(BillForm::class)
        ->set('bill', auth()->user()->bills->first())
        ->call('delete')
        ->assertHasNoErrors()
        ->assertRedirectToRoute('bill-calendar');
});

it('can delete a bill and its children', function () {
    livewire(BillForm::class)
        ->set('bill', auth()->user()->bills->first())
        ->call('delete', all: true)
        ->assertHasNoErrors()
        ->assertRedirectToRoute('bill-calendar');
});

test('component can render', function () {
    livewire(BillForm::class)
        ->assertHasNoErrors();
});
