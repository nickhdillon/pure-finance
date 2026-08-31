<?php

declare(strict_types=1);

use App\Livewire\BillCalendar;
use App\Models\Account;
use App\Models\Bill;
use App\Models\Category;
use App\Models\User;
use Livewire\Livewire;

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

test('calendar is the default view and list view is stored in the url', function () {
    livewire(BillCalendar::class)
        ->assertSet('view', 'calendar')
        ->set('view', 'list')
        ->assertSet('view', 'list')
        ->assertHasNoErrors();

    Livewire::withQueryParams(['view' => 'list'])
        ->test(BillCalendar::class)
        ->assertSet('view', 'list');
});

test('list view groups bills by date and shows their total', function () {
    $user = auth()->user();

    $user->bills()->delete();

    Bill::factory()->for($user)->create([
        'name' => 'Internet',
        'amount' => 75.25,
        'date' => '2026-09-10',
        'paid' => false,
    ]);

    Bill::factory()->for($user)->create([
        'name' => 'Electricity',
        'amount' => 124.75,
        'date' => '2026-09-10',
        'paid' => true,
    ]);

    livewire(BillCalendar::class, ['view' => 'list'])
        ->assertSee('Thursday, September 10, 2026')
        ->assertSee('Internet')
        ->assertSee('Electricity')
        ->assertSee('Today')
        ->assertSee('$200.00')
        ->assertSeeHtml('flex flex-col gap-2')
        ->assertSeeHtml('grow min-h-0 overflow-y-auto')
        ->assertSeeHtml('shrink-0 flex items-center justify-between')
        ->assertSeeHtml('bg-amber-400/25')
        ->assertSeeHtml('bg-emerald-400/25')
        ->assertHasNoErrors();
});
