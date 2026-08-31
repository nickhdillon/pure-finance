<?php

declare(strict_types=1);

use App\Enums\RecurringFrequency;
use App\Enums\TransactionType;
use App\Livewire\BillForm;
use App\Models\Account;
use App\Models\Bill;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
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

it('creates monthly bills on the last day of each month when starting at month end', function () {
    Carbon::setTestNow(Carbon::parse('2026-08-01 12:00:00', 'America/Chicago'));

    $user = User::first();

    livewire(BillForm::class)
        ->set('account_id', $user->accounts()->first()->id)
        ->set('name', 'Month End Bill')
        ->set('type', TransactionType::DEBIT)
        ->set('category_id', $user->categories()->first()->id)
        ->set('amount', 100)
        ->set('date', '2026-08-31')
        ->set('frequency', RecurringFrequency::MONTHLY)
        ->call('submit')
        ->assertHasNoErrors();

    $bill = Bill::where('name', 'Month End Bill')->whereNull('parent_id')->firstOrFail();

    expect($bill->children()->orderBy('date')->pluck('date')->map->toDateString()->all())
        ->toBe([
            '2026-09-30',
            '2026-10-31',
            '2026-11-30',
            '2026-12-31',
        ]);

    Carbon::setTestNow();
});

it('keeps calendar-based recurring bills at month end', function (
    RecurringFrequency $frequency,
    string $start_date,
    string $expected_date,
) {
    Carbon::setTestNow(Carbon::parse('2026-01-01 12:00:00', 'America/Chicago'));

    $user = User::first();

    livewire(BillForm::class)
        ->set('account_id', $user->accounts()->first()->id)
        ->set('name', 'Calendar Month End Bill')
        ->set('type', TransactionType::DEBIT)
        ->set('category_id', $user->categories()->first()->id)
        ->set('amount', 100)
        ->set('date', $start_date)
        ->set('frequency', $frequency)
        ->call('submit')
        ->assertHasNoErrors();

    $bill = Bill::where('name', 'Calendar Month End Bill')->whereNull('parent_id')->firstOrFail();

    expect($bill->children()->orderBy('date')->firstOrFail()->date->toDateString())
        ->toBe($expected_date);

    Carbon::setTestNow();
})->with([
    'quarterly' => [RecurringFrequency::QUARTERLY, '2026-01-31', '2026-04-30'],
    'semi-annually' => [RecurringFrequency::SEMI_ANNUALLY, '2026-01-31', '2026-07-31'],
    'yearly from leap day' => [RecurringFrequency::YEARLY, '2024-02-29', '2025-02-28'],
]);

it('keeps bi-weekly bills on an exact two-week interval', function () {
    Carbon::setTestNow(Carbon::parse('2026-08-01 12:00:00', 'America/Chicago'));

    $user = User::first();

    livewire(BillForm::class)
        ->set('account_id', $user->accounts()->first()->id)
        ->set('name', 'Bi-weekly Bill')
        ->set('type', TransactionType::DEBIT)
        ->set('category_id', $user->categories()->first()->id)
        ->set('amount', 100)
        ->set('date', '2026-08-31')
        ->set('frequency', RecurringFrequency::BI_WEEKLY)
        ->call('submit')
        ->assertHasNoErrors();

    $bill = Bill::where('name', 'Bi-weekly Bill')->whereNull('parent_id')->firstOrFail();

    expect($bill->children()->orderBy('date')->firstOrFail()->date->toDateString())
        ->toBe('2026-09-14');

    Carbon::setTestNow();
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
        'name' => 'Test',
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
