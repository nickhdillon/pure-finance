<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Enums\TransactionType;
use App\Livewire\TransactionTable;
use Illuminate\Support\Facades\URL;
use function Pest\Livewire\livewire;
use Illuminate\Database\Eloquent\Model;

beforeEach(function () {
    $this->withoutDefer();

    $user = User::factory()->create();

    if (Category::count() === 0) {
        $parent_categories = collect([
            'Personal Income',
            'Pets',
            'Shopping',
            'Travel',
            'Utilities',
        ]);

        $child_categories = collect([
            'Paycheck',
            'Dog Food',
            'Gifts',
            'Hotel',
            'Water',
        ]);

        $parent_categories = $parent_categories->map(function (string $parent) use ($user): Model {
            return $user->categories()->create(['name' => $parent]);
        });

        $parent_index = 0;

        $child_categories->each(function (string $child, int $index) use ($parent_categories, &$parent_index, $user): void {
            $parent = $parent_categories->get($parent_index);

            $user->categories()->create([
                'name' => $child,
                'parent_id' => $parent->id,
            ]);

            if (($index + 1) % 2 === 0) {
                $parent_index++;
            }
        });
    }

    Account::factory()
        ->for($user)
        ->has(Transaction::factory()->count(10))
        ->create();

    $this->actingAs($user);
});

it('can update search', function () {
    livewire(TransactionTable::class)
        ->set('search', 'Test')
        ->assertHasNoErrors();

    expect(Str::contains(URL::current(), '?page'))
        ->toBeFalse();
});

it('can update status', function () {
    livewire(TransactionTable::class)
        ->set('status', 'cleared')
        ->assertHasNoErrors();
});

it('can toggle status', function () {
    livewire(TransactionTable::class)
        ->call('toggleStatus', Transaction::first()->id)
        ->assertDispatched('status-changed')
        ->assertHasNoErrors();
});

it('can sort by account name', function () {
    livewire(TransactionTable::class)
        ->call('sortBy', 'account')
        ->assertHasNoErrors();
});

it('can sort by category name', function () {
    livewire(TransactionTable::class)
        ->call('sortBy', 'category')
        ->assertHasNoErrors();
});

it('can sort by type', function () {
    livewire(TransactionTable::class)
        ->call('sortBy', 'type')
        ->assertHasNoErrors();
});

it('can sort by amount', function () {
    livewire(TransactionTable::class)
        ->call('sortBy', 'amount')
        ->assertHasNoErrors();
});

it('can sort by payee', function () {
    livewire(TransactionTable::class)
        ->call('sortBy', 'payee')
        ->assertHasNoErrors();
});

it('can sort by date', function () {
    livewire(TransactionTable::class)
        ->call('sortBy', 'date')
        ->assertHasNoErrors();
});

it('can sort by status', function () {
    livewire(TransactionTable::class)
        ->set('sort_col', 'status')
        ->call('sortBy', 'status')
        ->assertHasNoErrors();
});

it('can filter by transaction type', function () {
    livewire(TransactionTable::class)
        ->set('transaction_type', TransactionType::DEPOSIT)
        ->assertHasNoErrors();
});

it('can filter by selected accounts', function () {
    livewire(TransactionTable::class)
        ->set('selected_accounts', [
            auth()->user()->accounts->first()->name,
        ])
        ->assertHasNoErrors();
});

it('can filter by selected parent with child categories', function () {
    livewire(TransactionTable::class)
        ->set('selected_categories', [
            auth()->user()->categories()->where('parent_id', null)->first()->name,
        ])
        ->assertHasNoErrors();
});

it('can filter by child category', function () {
    livewire(TransactionTable::class)
        ->set('selected_categories', [
            auth()->user()->categories()->where('parent_id', ! null)->first()->name,
        ])
        ->assertHasNoErrors();
});

it('can filter by date', function () {
    livewire(TransactionTable::class)
        ->set('date', now()->subWeek())
        ->assertHasNoErrors();
});

it('can clear filters', function () {
    livewire(TransactionTable::class)
        ->call('clearFilters')
        ->assertHasNoErrors();
});

it('can delete a transaction', function () {
    livewire(TransactionTable::class)
        ->call('delete', Transaction::first()->id)
        ->assertHasNoErrors();
});

test('component can render with account', function () {
    livewire(TransactionTable::class, ['account' => Account::first()])
        ->assertHasNoErrors();
});

test('component can render', function () {
    livewire(TransactionTable::class)
        ->assertHasNoErrors();
});
