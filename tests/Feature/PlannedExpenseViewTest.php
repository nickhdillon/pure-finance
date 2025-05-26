<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\PlannedExpense;
use App\Livewire\PlannedExpenseView;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $user = User::factory()->create();

    $this->actingAs($user);

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
                'name' => $name
            ]);
        });
    }

    $this->actingAs($user);
});

it('can load transactions for current month', function () {
    $expense = PlannedExpense::factory()->create();

    $transactions = Transaction::factory(10)->state([
        'category_id' => $expense->category_id,
        'account_id' => Account::factory()->for(auth()->user()),
        'date' => now('America/Chicago')
    ])->create();

    livewire(PlannedExpenseView::class, ['expense' => $expense])
        ->call('loadTransactions')
        ->assertSet('transactions.*.id', $transactions->pluck('id')->toArray())
        ->assertSet('selected_month', now('America/Chicago')->format('F') . ' (' . count($transactions->pluck('id')->toArray()) . ')')
        ->assertHasNoErrors();
});

it('can load transactions for previous month', function () {
    $expense = PlannedExpense::factory()->create();

    $transactions = Transaction::factory(10)->state([
        'category_id' => $expense->category_id,
        'account_id' => Account::factory()->for(auth()->user()),
        'date' => now('America/Chicago')->subMonth()
    ])->create();

    livewire(PlannedExpenseView::class, ['expense' => $expense])
        ->call('loadTransactions', now('America/Chicago')->subMonth()->format('M'))
        ->assertSet('transactions.*.id', $transactions->pluck('id')->toArray())
        ->assertSet('selected_month', now('America/Chicago')->subMonth()->format('F') . ' (' . count($transactions->pluck('id')->toArray()) . ')')
        ->assertHasNoErrors();
});

it('can reset transactions', function () {
    livewire(PlannedExpenseView::class, ['expense' => PlannedExpense::factory()->create()])
        ->call('resetTransactions')
        ->assertSet('transactions', null)
        ->assertHasNoErrors();
});

test('component can render', function () {
    livewire(PlannedExpenseView::class, ['expense' => PlannedExpense::factory()->create()])
        ->assertHasNoErrors();
});
