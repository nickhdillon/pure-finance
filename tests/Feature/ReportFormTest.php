<?php

declare(strict_types=1);

use App\Models\Tag;
use App\Models\User;
use App\Models\Report;
use Prism\Prism\Prism;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Livewire\ReportForm;
use App\Enums\TransactionType;
use function Pest\Livewire\livewire;
use Prism\Prism\Testing\TextResponseFake;

beforeEach(function () {
    $this->actingAs(
        User::factory()
            ->has(Account::factory())
            ->has(Report::factory())
            ->has(Tag::factory(5))
            ->create()
    );
});

it('can create a new report', function () {
    $fake_response = TextResponseFake::make()
        ->withText('Hello, I am OpenAI!');

    Prism::fake([$fake_response]);

    $now = now();

    Transaction::factory(10)
        ->for(Account::first())
        ->hasAttached(Tag::first())
        ->create([
            'type' => TransactionType::DEBIT,
            'payee' => 'Test Payee',
            'date' => (clone ($now)->subWeek())->format('Y-m-d')
        ]);

    livewire(ReportForm::class)
        ->set('name', 'Test name')
        ->set('account_id', Account::first()->id)
        ->set('type', TransactionType::DEBIT)
        ->set('payees', ['Test Payee'])
        ->set('category_id', Category::first()->id)
        ->set('tag_id', Tag::first()->id)
        ->set('date_range', [
            'start' => (clone $now)->subMonth(),
            'end' => $now
        ])
        ->call('submit')
        ->assertHasNoErrors();

    $this->assertDatabaseCount('report_transactions', 10);
});

test('component can render', function () {
    livewire(ReportForm::class)
        ->assertHasNoErrors();
});
