<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Account;
use App\Enums\AccountType;
use App\Models\Transaction;
use App\Enums\TransactionType;
use Illuminate\Support\Carbon;
use App\Livewire\NetWorthHistory;
use function Pest\Livewire\livewire;

beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2026-07-27 12:00:00', 'America/Chicago'));
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

afterEach(function () {
    Carbon::setTestNow();
});

it('shows net worth at the selected point in time', function () {
    $checking = Account::factory()->for($this->user)->create([
        'type' => AccountType::CHECKING,
        'initial_balance' => 1000,
    ]);

    $credit_card = Account::factory()->for($this->user)->create([
        'type' => AccountType::CREDIT_CARD,
        'initial_balance' => 200,
    ]);

    Transaction::factory()->for($checking)->create([
        'type' => TransactionType::DEPOSIT,
        'amount' => 500,
        'date' => '2026-07-20',
    ]);

    Transaction::factory()->for($checking)->create([
        'type' => TransactionType::DEBIT,
        'amount' => 100,
        'date' => '2026-07-28',
    ]);

    Transaction::factory()->for($credit_card)->create([
        'type' => TransactionType::DEBIT,
        'amount' => 50,
        'date' => '2026-07-25',
    ]);

    livewire(NetWorthHistory::class)
        ->assertSee('$1,350.00')
        ->call('nextDay')
        ->assertSet('selected_date', '2026-07-28')
        ->assertSee('$1,250.00')
        ->call('previousDay')
        ->assertSet('selected_date', '2026-07-27');
});

it('can jump to an arbitrary date and return to today', function () {
    livewire(NetWorthHistory::class)
        ->set('selected_date', '2025-01-15')
        ->assertViewHas('chart_end_label', 'Jan 15, 2025')
        ->call('goToToday')
        ->assertSet('selected_date', '2026-07-27')
        ->assertViewHas('chart_end_label', 'Jul 27, 2026')
        ->assertHasNoErrors();
});

test('chart shows the six months ending on the selected date', function () {
    livewire(NetWorthHistory::class)
        ->set('selected_date', '2030-01-15')
        ->assertViewHas('chart_data', function ($chart_data): bool {
            return $chart_data->count() === 6
                && $chart_data->first()['date'] === '2029-08-15'
                && $chart_data->first()['position'] === 0
                && $chart_data->first()['axis_label'] === 'Aug 15'
                && $chart_data->last()['date'] === '2030-01-15'
                && $chart_data->last()['position'] === 5
                && $chart_data->last()['axis_label'] === 'Jan 15'
                && $chart_data->pluck('date')->values()->all()
                    === $chart_data->pluck('date')->sort()->values()->all();
        })
        ->assertViewHas('chart_start_label', 'Aug 15, 2029')
        ->assertViewHas('chart_end_label', 'Jan 15, 2030');
});

test('chart end label changes when navigating dates', function () {
    livewire(NetWorthHistory::class)
        ->assertViewHas('chart_end_label', 'Jul 27, 2026')
        ->call('nextDay')
        ->assertViewHas('chart_end_label', 'Jul 28, 2026')
        ->call('previousDay')
        ->assertViewHas('chart_end_label', 'Jul 27, 2026');
});

test('clearing the selected date returns to today', function (?string $cleared_value) {
    livewire(NetWorthHistory::class)
        ->set('selected_date', $cleared_value)
        ->assertSet('selected_date', '2026-07-27')
        ->assertViewHas('chart_end_label', 'Jul 27, 2026')
        ->assertHasNoErrors();
})->with([
    'empty string' => '',
    'null' => null,
]);
