<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use App\Models\Account;
use App\Enums\AccountType;
use Carbon\CarbonImmutable;
use Livewire\Attributes\On;
use App\Enums\TransactionType;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

#[On('account-saved'), On('transaction-saved'), On('transaction-deleted'), On('status-changed')]
class NetWorthHistory extends Component
{
    public ?string $selected_date = null;

    public function mount(): void
    {
        $this->selected_date = now('America/Chicago')->toDateString();
    }

    public function previousDay(): void
    {
        $this->selected_date = $this->selectedDate()->subDay()->toDateString();
    }

    public function nextDay(): void
    {
        $this->selected_date = $this->selectedDate()->addDay()->toDateString();
    }

    public function goToToday(): void
    {
        $this->selected_date = now('America/Chicago')->toDateString();
    }

    public function updatedSelectedDate(): void
    {
        if (blank($this->selected_date)) {
            $this->goToToday();
            $this->resetValidation('selected_date');

            return;
        }

        $this->validateOnly('selected_date', [
            'selected_date' => ['required', 'date_format:Y-m-d'],
        ]);
    }

    private function selectedDate(): CarbonImmutable
    {
        if (blank($this->selected_date)) {
            return CarbonImmutable::now('America/Chicago')->startOfDay();
        }

        return CarbonImmutable::createFromFormat('!Y-m-d', $this->selected_date, 'America/Chicago')
            ?: CarbonImmutable::now('America/Chicago')->startOfDay();
    }

    /**
     * @return Collection<int, array{position: int, date: string, axis_label: string, net_worth: float}>
     */
    private function chartData(CarbonImmutable $end): Collection
    {
        $sample_dates = collect(range(5, 0))
            ->map(fn (int $months_ago): CarbonImmutable => $end->subMonthsNoOverflow($months_ago));

        $transaction_sums = [];

        foreach ($sample_dates as $position => $date) {
            $transaction_sums["transactions as deposits_{$position}"] =
                fn (Builder $query) => $query
                    ->whereIn('type', [TransactionType::CREDIT, TransactionType::DEPOSIT])
                    ->whereDate('date', '<=', $date->toDateString());

            $transaction_sums["transactions as debits_{$position}"] =
                fn (Builder $query) => $query
                    ->whereIn('type', [
                        TransactionType::DEBIT,
                        TransactionType::TRANSFER,
                        TransactionType::WITHDRAWAL,
                    ])
                    ->whereDate('date', '<=', $date->toDateString());
        }

        $accounts = auth()->user()->accounts()
            ->withSum($transaction_sums, 'amount')
            ->get();

        return $sample_dates->values()->map(function (CarbonImmutable $date, int $position) use ($accounts): array {
            return [
                'position' => $position,
                'date' => $date->toDateString(),
                'axis_label' => $date->format('M j'),
                'net_worth' => round(
                    $accounts->sum(
                        fn (Account $account): float => $this->accountNetWorth(
                            $account,
                            (float) ($account->{"deposits_{$position}"} ?? 0),
                            (float) ($account->{"debits_{$position}"} ?? 0),
                        )
                    ),
                    2
                ),
            ];
        });
    }

    private function accountNetWorth(Account $account, float $deposits, float $debits): float
    {
        $is_debt = in_array($account->type, [AccountType::LOAN, AccountType::CREDIT_CARD], true);

        $balance = $is_debt
            ? (float) $account->initial_balance - $debits + $deposits
            : (float) $account->initial_balance + $deposits - $debits;

        return $is_debt ? -$balance : $balance;
    }

    public function render(): View
    {
        $selected_date = $this->selectedDate();
        $chart_data = $this->chartData($selected_date);

        return view('livewire.net-worth-history', [
            'chart_data' => $chart_data,
            'chart_start_label' => CarbonImmutable::parse(
                $chart_data->first()['date'],
                'America/Chicago'
            )->format('M j, Y'),
            'chart_end_label' => $selected_date->format('M j, Y'),
            'net_worth' => $chart_data->last()['net_worth'] ?? 0.0,
            'selected_date_label' => $selected_date->format('F j, Y'),
        ]);
    }
}
