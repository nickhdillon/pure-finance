<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Enums\TransactionType;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;

class MonthlySpendingOverview extends Component
{
    public string $month_short;

    public string $month_full;

    public float $monthly_total = 0;

    public Collection $top_categories;

    private array $colors = [
        ['base' => 'bg-emerald-400', 'hex' => '#00D492'],
        ['base' => 'bg-orange-500', 'hex' => '#FF6900'],
        ['base' => 'bg-indigo-500', 'hex' => '#615FFF'],
        ['base' => 'bg-amber-500', 'hex' => '#FFB900'],
        ['base' => 'bg-red-500', 'hex' => '#FA2C36'],
    ];

    public string $gradient = '';

    public function mount(): void
    {
        $user = auth()->user();

        $now = now('America/Chicago');

        $this->month_full = $now->copy()->format('F');

        $this->month_short = Str::length($this->month_full) > 5
            ? $now->copy()->format('M')
            : $this->month_full;

        $start_of_month = $now->copy()->startOfMonth()->toDateString();
        $end_of_month = $now->copy()->endOfMonth()->toDateString();

        $this->monthly_total = $user->transactions()
            ->where('transactions.type', TransactionType::DEBIT)
            ->whereBetween('transactions.date', [$start_of_month, $end_of_month])
            ->sum('transactions.amount');

        $this->top_categories = $user->categories()
            ->selectRaw('categories.name, SUM(transactions.amount) as total_spent')
            ->join('transactions', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.type', TransactionType::DEBIT)
            ->whereBetween('transactions.date', [$start_of_month, $end_of_month])
            ->groupBy('categories.name')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get()
            ->values()
            ->map(function (Category $category, int $index): Category {
                $category->percent = $this->monthly_total > 0
                    ? ($category->total_spent / $this->monthly_total) * 100
                    : 0;

                $category->color = $this->colors[$index]['base'];
                $category->hex = $this->colors[$index]['hex'];

                return $category;
            });

        $this->gradient = $this->buildGradient($this->top_categories);
    }

    private function buildGradient(Collection $categories): string
    {
        $start = 0;

        return $categories->map(function (Category $category) use (&$start): string {
            $end = $start + $category->percent;

            $segment = "{$category->hex} {$start}% {$end}%";

            $start = $end;

            return $segment;
        })->implode(',');
    }

    public function render(): View
    {
        return view('livewire.monthly-spending-overview');
    }
}
