<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\IncomeType;
use App\Models\Bill;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class MonthlyOverview extends Component
{
    public string $month = '';

    public function mount(): void
    {
        $this->month = now('America/Chicago')->format('Y-m');
    }

    public function previousMonth(): void
    {
        $this->month = $this->selectedMonth()->subMonth()->format('Y-m');
    }

    public function nextMonth(): void
    {
        $this->month = $this->selectedMonth()->addMonth()->format('Y-m');
    }

    public function currentMonth(): void
    {
        $this->month = now('America/Chicago')->format('Y-m');
    }

    private function selectedMonth(): CarbonImmutable
    {
        return CarbonImmutable::createFromFormat('!Y-m', $this->month, 'America/Chicago')
            ?: CarbonImmutable::now('America/Chicago')->startOfMonth();
    }

    #[Computed]
    public function incomeCards(): array
    {
        $incomes = auth()
            ->user()
            ->incomes()
            ->whereBetween('date', [$this->start->toDateString(), $this->end->toDateString()])
            ->latest('date')
            ->latest('id')
            ->get();

        if ($incomes->isEmpty()) {
            return [];
        }

        $expected = $incomes
            ->where('type', IncomeType::EXPECTED)
            ->values();

        $unplanned = $incomes
            ->where('type', IncomeType::UNPLANNED)
            ->values();

        $cards = [
            [
                'name' => 'Expected',
                'total' => $expected->sum('amount'),
                'incomes' => $expected,
            ],
        ];

        if ($unplanned->isNotEmpty()) {
            $cards[] = [
                'name' => 'Unplanned',
                'total' => $unplanned->sum('amount'),
                'incomes' => $unplanned,
            ];
        }

        return $cards;
    }

    #[Computed]
    public function incomeTotal(): float|int
    {
        return collect($this->incomeCards)->sum('total');
    }

    #[Computed]
    public function bills(): array
    {
        $rows = auth()
            ->user()
            ->bills()
            ->whereBetween('date', [$this->start->toDateString(), $this->end->toDateString()])
            ->orderBy('date')
            ->get()
            ->map(fn (Bill $bill): array => [
                'id' => $bill->id,
                'name' => $bill->name,
                'date' => $bill->date->format('M j, Y'),
                'amount' => (float) $bill->amount,
                'paid' => $bill->paid,
            ])
            ->values();

        $total = (float) $rows->sum('amount');
        $paid_total = (float) $rows->where('paid', true)->sum('amount');
        $unpaid_total = $total - $paid_total;

        return [
            'rows' => $rows,
            'total' => $total,
            'paid_total' => $paid_total,
            'unpaid_total' => $unpaid_total,
        ];
    }

    #[Computed]
    public function start(): CarbonImmutable
    {
        return $this->selectedMonth()->startOfMonth();
    }
 
    #[Computed]
    public function end(): CarbonImmutable
    {
        return $this->start()->endOfMonth();
    }
 
    #[Computed]
    public function currentDate(): CarbonImmutable
    {
        $today = CarbonImmutable::now('America/Chicago');
 
        if ($today->lessThan($this->start())) {
            return $this->start()->subDay();
        }
 
        if ($today->greaterThan($this->end())) {
            return $this->end();
        }
 
        return $today;
    }

    public function render(): View
    {
        return view('livewire.monthly-overview');
    }
}
