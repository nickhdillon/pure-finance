<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Facades\Queue;
use Illuminate\Database\Eloquent\Model;
use App\Jobs\ProcessRecurringTransactionsJob;

beforeEach(function () {
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
            'Water'
        ]);

        $parent_categories = $parent_categories->map(function (string $parent) use ($user): Model {
            return $user->categories()->create(['name' => $parent]);
        });

        $parent_index = 0;

        $child_categories->each(function (string $child, int $index) use ($parent_categories, &$parent_index, $user): void {
            $parent = $parent_categories->get($parent_index);

            $user->categories()->create([
                'name' => $child,
                'parent_id' => $parent->id
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

test('dispatched on default queue', function () {
    Queue::fake();

    $this->app->make(ProcessRecurringTransactionsJob::class)->dispatch();

    Queue::assertPushed(ProcessRecurringTransactionsJob::class);
});

test('no recurring transactions found', function () {
    $this->app->make(ProcessRecurringTransactionsJob::class)->dispatch();

    $this->assertDatabaseEmpty('jobs');
});

it('can process recurring transactions', function () {
    Account::factory()
        ->for(User::first())
        ->has(
            Transaction::factory(10)->state([
                'parent_id' => 1,
                'is_recurring' => true,
                'date' => now()->subDay()->timezone('America/Chicago')
            ])
        )
        ->create();

    $this->app->make(ProcessRecurringTransactionsJob::class)->dispatch();

    $this->assertDatabaseEmpty('jobs');
});

test('job fails', function () {
    $this->app->make(ProcessRecurringTransactionsJob::class)->failed(new Exception);

    $this->assertDatabaseEmpty('jobs');
});
