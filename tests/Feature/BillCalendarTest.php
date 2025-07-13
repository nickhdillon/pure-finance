<?php

declare(strict_types=1);

use App\Models\Bill;
use App\Models\User;
use App\Models\Account;
use App\Models\Category;
use App\Livewire\BillCalendar;
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
        ->create();

    $this->actingAs($user);
});

test('component can render', function () {
    livewire(BillCalendar::class)
        ->assertHasNoErrors();
});
